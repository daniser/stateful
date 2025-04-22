<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use TTBooking\Stateful\Facades;
use TTBooking\Stateful\StatefulServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [StatefulServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Stateful' => Facades\Stateful::class,
            'SFSerializer' => Facades\Serializer::class,
            'SFState' => Facades\State::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        //
    }
}
