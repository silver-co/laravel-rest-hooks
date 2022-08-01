<?php

declare(strict_types=1);

namespace SilverCO\RestHooks\Enums;

enum HttpMethods: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case PATCH = 'patch';
    case DELETE = 'delete';

    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
