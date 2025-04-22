<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\Service;
use TTBooking\Stateful\State;

/**
 * @method static Service service(string $name = null)
 * @method static Result query(Query $query)
 * @method static bool has(string $id)
 * @method static State get(string $id)
 * @method static State put(State $state)
 *
 * @see \TTBooking\Stateful\ServiceManager
 */
class Stateful extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'stateful-service';
    }
}
