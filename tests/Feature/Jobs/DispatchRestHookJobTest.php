<?php

namespace SilverCO\RestHooks\Tests\Feature\Jobs;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use SilverCO\RestHooks\Events\HookResponseWasFailed;
use SilverCO\RestHooks\Events\HookResponseWasSuccessful;
use SilverCO\RestHooks\Jobs\DispatchRestHookJob;
use SilverCO\RestHooks\Models\RestHook;
use SilverCO\RestHooks\Tests\TestCase;

class DispatchRestHookJobTest extends TestCase
{
    const TEST_EVENT = 'test.event';

    const TEST_SIGNATURE = 'A_RANDOM_SIGNATURE_STRING';

    const TEST_PAYLOAD = [
        'foo' => 'bar',
        'bar' => 'tar',
    ];

    private Collection $restHooks;

    public function setUp(): void
    {
        parent::setUp();

        $this->restHooks = RestHook::factory()->count(10)->unsigned()->create([
            'event' => self::TEST_EVENT,
        ]);
    }

    /** @test */
    public function dispatchesCallsToRegisteredHooksURLs()
    {
        Http::fake();
        Event::fake();

        $job = new DispatchRestHookJob(self::TEST_EVENT, self::TEST_PAYLOAD);

        $job->handle();

        Http::assertSentCount(10);

        Event::assertDispatched(HookResponseWasSuccessful::class, 10);
        Event::assertNotDispatched(HookResponseWasFailed::class);
    }

    /** @test */
    public function dispatchesCallsToRegisteredHooksURLsWithSignature()
    {
        Http::fake();
        Event::fake();

        RestHook::whereNull('signature')->update([
            'signature' => self::TEST_SIGNATURE,
        ]);

        $this->restHooks = RestHook::all();

        $job = new DispatchRestHookJob(self::TEST_EVENT, self::TEST_PAYLOAD);

        $job->handle();

        Http::assertSentCount(10);
        foreach ($this->restHooks as $hook) {
            Http::assertSent(function (Request $request) use ($hook) {
                return $this->getRequestWasSent($request, $hook) ||
                    $this->requestDifferentThanGetWasSent($request, $hook);
            });
        }
        Event::assertDispatched(HookResponseWasSuccessful::class, 10);
        Event::assertNotDispatched(HookResponseWasFailed::class);
    }

    /** @test */
    public function dispatchesCallsToRegisteredHooksURLsFailures()
    {
        Http::fake([
            '*' => Http::response('Not Found', 400, ['Headers']),
        ]);
        Event::fake();

        $job = new DispatchRestHookJob(self::TEST_EVENT, self::TEST_PAYLOAD);

        $job->handle();

        Http::assertSentCount(10);
        Event::assertDispatched(HookResponseWasFailed::class);
        Event::assertNotDispatched(HookResponseWasSuccessful::class, 10);
    }

    private function requestDifferentThanGetWasSent(Request $request, RestHook $hook)
    {
        return $this->signatureHeaderWasSent($request) &&
            json_decode($request->body(), true) === self::TEST_PAYLOAD &&
            $request->url() === $hook->target;
    }

    private function getRequestWasSent(Request $request, RestHook $hook)
    {
        $payload = http_build_query(self::TEST_PAYLOAD);

        return $this->signatureHeaderWasSent($request) &&
            $request->body() === '' &&
            $request->url() === "$hook->target?$payload";
    }

    private function signatureHeaderWasSent(Request $request)
    {
        return $request->hasHeader(Config::get('resthooks.signature_header'), self::TEST_SIGNATURE);
    }
}
