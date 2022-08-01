<?php

declare(strict_types=1);

namespace SilverCO\RestHooks\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use SilverCO\RestHooks\Enums\HttpMethods;
use SilverCO\RestHooks\Models\RestHook;

class RestHookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RestHook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userClass = Config::get('resthooks.auth_model');

        return [
            'user_id' => $userClass::factory()->create()->id,
            'event' => $this->faker->unique()->safeEmail(),
            'target' => $this->faker->unique()->url(),
            'method' => $this->faker->randomElement(HttpMethods::toArray()),
            'signature' => Hash::make($this->faker->randomKey()),
        ];
    }

    /**
     * Indicate that the REST Hook does not need a signature.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unsigned()
    {
        return $this->state(function () {
            return [
                'signature' => null,
            ];
        });
    }
}
