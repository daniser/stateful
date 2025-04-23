<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\Stateful\Contracts\Client as ClientContract;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;

/**
 * @method static ClientContract connection(string $name = null)
 * @method static Result query(Query $query)
 *
 * @see \TTBooking\Stateful\ConnectionManager
 */
class Client extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'stateful-client';
    }
}
