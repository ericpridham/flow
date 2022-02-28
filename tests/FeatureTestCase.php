<?php

namespace EricPridham\Flow\Tests;

use EricPridham\Flow\FlowServiceProvider;
use Orchestra\Testbench\TestCase;

class FeatureTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FlowServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
    }
}
