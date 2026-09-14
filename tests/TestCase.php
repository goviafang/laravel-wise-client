<?php

declare(strict_types=1);

namespace Govia\WiseClient\Tests;

use Govia\WiseClient\WiseServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [WiseServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('wise.environment', 'sandbox');
        $app['config']->set('wise.auth.driver', 'personal_token');
        $app['config']->set('wise.auth.personal_token.token', 'test-token');
    }
}
