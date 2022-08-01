<?php

namespace SilverCO\RestHooks\Http\Requests;

use Illuminate\Validation\Rule;
use SilverCO\RestHooks\Enums\HttpMethods;

class StoreRequest extends BaseJSONRequest
{
    public function rules()
    {
        return [
            'event' => ['required'],
            'target' => ['required', 'url'],
            'signature' => ['sometimes', 'string'],
            'method' => [Rule::in(HttpMethods::toArray())],
        ];
    }
}
