<?php

namespace SilverCO\RestHooks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\SerializesModels;

class HookResponseWasFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Response $response,
        public array $payload
    ) {
    }
}
