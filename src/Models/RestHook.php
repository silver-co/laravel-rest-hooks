<?php

namespace SilverCO\RestHooks\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SilverCO\RestHooks\Database\Factories\RestHookFactory;
use SilverCO\RestHooks\Events\HookResponseWasFailed;
use SilverCO\RestHooks\Events\HookResponseWasSuccessful;

class RestHook extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'event', 'target', 'method', 'signature', 'user_id'
    ];

    use HasFactory;

    /**
     * Send a request to the target url with the given payload.
     *
     * @param  array  $payload
     * @return Response
     */
    public function dispatch(array $payload): Response
    {
        try {
            $method = $this->method;
            $signatureHeader = Config::get('resthooks.signature_header');;
            $headers = $this->signature ? [$signatureHeader => $this->signature] : [];
            $response = Http::withHeaders($headers)->$method($this->target, $payload);

            $this->handleResponse($response, $payload);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

        return $response;
    }

    /**
     * Handle logic for a dispatched hook.
     *
     * @param  Response  $response
     * @return void
     */
    private function handleResponse(Response $response, array $payload)
    {
        if ($response->failed()) {
            HookResponseWasFailed::dispatch($response, $payload);
        }

        if ($response->ok()) {
            HookResponseWasSuccessful::dispatch($response);
        }
    }

    /**
     * Get the REST Hook owner model.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Config::get('resthooks.auth_model'));
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \SilverCO\Database\Factories\RestHookFactory
     */
    protected static function newFactory(): RestHookFactory
    {
        return RestHookFactory::new();
    }
}
