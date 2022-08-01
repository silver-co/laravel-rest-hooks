<?php

namespace SilverCO\RestHooks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\SerializesModels;

class HookResponseWasSuccessful
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Response $response
    ) {
    }
}
