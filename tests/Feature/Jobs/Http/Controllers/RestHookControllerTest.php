<?php

namespace SilverCO\RestHooks\Tests\Feature\Jobs;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use SilverCO\RestHooks\Enums\HttpMethods;
use SilverCO\RestHooks\Models\RestHook;
use SilverCO\RestHooks\Tests\Common\UserModel;
use SilverCO\RestHooks\Tests\TestCase;

class RestHookControllerTest extends TestCase
{
    use WithFaker;

    const TEST_EVENT = 'test.event';

    public function setUp(): void
    {
        parent::setUp();

        $this->actingAs(UserModel::factory()->create());
    }

    /**
     * @test
     */
    public function storeSuccess()
    {
        $response = $this->post('/api/hooks', [
            'event' => self::TEST_EVENT,
            'target' => $target = $this->faker->url(),
            'signature' => $signature = $this->faker->randomKey(),
            'method' => $method = $this->faker->randomElement(HttpMethods::toArray()),
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'id' => 1,
                'event' => self::TEST_EVENT,
                'target' => $target,
                'signature' => $signature,
                'method' => $method,
            ]);
    }

    /** 
     * @dataProvider storeValidationProvider
     * @test
     * 
     * @param string $field
     * @param mixed $value
     * @param string $message
     */
    public function storeValidations(string $field, mixed $value, string $message)
    {
        $this->expectException(ValidationException::class);

        $data = RestHook::factory()->makeOne()->toArray();
        $data[$field] = $value;

        $response = $this->post('/api/hooks', $data);

        $response->assertJsonValidationErrors([
            $field => $message
        ]);
    }

    public function storeValidationProvider(): array
    {
        return [
            'validate null value on event' => ['event', NULL, 'event field is required'],
            'validate empty string value on event' => ['event', '', 'event field is required'],
            'validate null value on target' => ['target', NULL, 'target field is required'],
            'validate empty string value on target' => ['target', '', 'target field is required'],
            'validate target is valid URL' => ['target', 'invalid url', 'target must be a valid URL'],
            'method should be valid' => ['method', 'Not valid method', 'selected method is invalid'],
        ];
    }

    /**
     * @group fail
     * @test
     */
    public function updateSuccess()
    {
        RestHook::factory()->create();
        $data = RestHook::factory()->make()->toArray();
        $response = $this->put('/api/hooks/1', $data);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Subscription has been updated.',
                'data' => $data,
            ]);
    }

    /**
     * @group fail
     * @dataProvider updateValidationProvider
     * @test
     *
     * @param string $field
     * @param mixed $value
     * @param string $message
     */
    public function updateValidations(string $field, mixed $value, string $message)
    {
        RestHook::factory()->create();
        $this->expectException(ValidationException::class);

        $data = RestHook::factory()->makeOne()->toArray();
        $data[$field] = $value;

        $response = $this->put('/api/hooks/1', $data);

        $response->assertJsonValidationErrors([
            $field => $message
        ]);
    }

    public function updateValidationProvider(): array
    {
        return [
            'validate target is valid URL' => ['target', 'invalid url', 'target must be a valid URL'],
            'method should be valid' => ['method', 'Not valid method', 'selected method is invalid'],
        ];
    }

    /** @test */
    public function updateShouldDeletePreviousUnhandledJobs()
    {
    }

    /** @test */
    public function destroySuccess()
    {
        $model = RestHook::factory()->create();
        $response = $this->post('/api/hooks/1');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'The subscription has been removed.',
                'data' => $model->toArray(),
            ]);
    }
}
