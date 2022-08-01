<?php

namespace SilverCO\RestHooks\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SilverCO\RestHooks\ServiceProvider;
use SilverCO\RestHooks\Tests\Common\UserModel;
use SilverCO\RestHooks\Tests\Common\UserTableMigration;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('resthooks.auth_model', UserModel::class);

        (new UserTableMigration)->up();
    }
}
