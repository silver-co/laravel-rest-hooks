<?php

namespace SilverCO\RestHooks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseJSONRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Overriding method to ensure json payload is prioritized.
     * @inheritdoc
     */
    public function all($keys = null)
    {
        $input = parent::all();
        $json = $this->json();

        return [...$input, ...$json];
    }
}
