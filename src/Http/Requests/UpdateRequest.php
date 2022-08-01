<?php

namespace SilverCO\RestHooks\Http\Requests;

use Illuminate\Validation\Rule;
use SilverCO\RestHooks\Enums\HttpMethods;

class UpdateRequest extends BaseJSONRequest
{
    public function rules()
    {
        return [
            'target' => ['url'],
            'signature' => ['sometimes', 'string'],
            'method' => [Rule::in(HttpMethods::toArray())],
        ];
    }
}
