<?php

namespace SilverCO\RestHooks\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use SilverCO\RestHooks\Events\HookResponseWasFailed;
use SilverCO\RestHooks\Events\HookResponseWasSuccessful;
use SilverCO\RestHooks\Models\RestHook;

class DispatchRestHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $event,
        protected array $payload
    ) {
    }

    public function handle()
    {
        $limit = Config::get('resthooks.batch.amount');
        $signatureHeader = Config::get('resthooks.signature_header');
        $requestsAmount = Config::get('resthooks.batch.requests');
        $payload = $this->payload;

        $registeredHooks = RestHook::where('event', $this->event)
            ->limit($limit)
            ->get();

        $registeredHooks->chunk($requestsAmount)->each(function ($chunk) use ($payload, $signatureHeader) {
            $responses = Http::pool(function (Pool $pool) use ($chunk, $payload, $signatureHeader) {
                return $chunk->map(function ($hook, $index) use ($pool, $payload, $signatureHeader) {
                    $headers = $hook->signature ? [$signatureHeader => $hook->signature] : [];

                    return $pool
                        ->as($index)
                        ->withHeaders($headers)
                        ->{$hook->method}($hook->target, $payload);
                });
            });

            $this->handleResponseGroup($responses);
        });
    }

    /**
     * Handle logic for a single response.
     *
     * @param  Response  $response
     * @return void
     */
    private function handleResponse(Response $response)
    {
        if ($response->failed()) {
            HookResponseWasFailed::dispatch($response, $this->payload);
        }

        if ($response->ok()) {
            HookResponseWasSuccessful::dispatch($response);
        }
    }

    /**
     * Handle logic for a group of responses.
     *
     * @param  Response  $response
     * @return void
     */
    private function handleResponseGroup(array $responses)
    {
        foreach ($responses as $response) {
            $this->handleResponse($response);
        }
    }
}
