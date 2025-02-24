<?php

namespace Plank\LaravelModelResolver\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Plank\LaravelModelResolver\LaravelModelResolverServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelModelResolverServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
