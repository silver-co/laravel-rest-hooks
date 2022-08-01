<?php

declare(strict_types=1);

namespace SilverCO\RestHooks\Tests\Common;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->email(),
            'fullname' => $this->faker->unique()->name(),
        ];
    }
}
