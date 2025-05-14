<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\Service;
use TTBooking\Stateful\State;

/**
 * @method static Service service(string $name = null)
 * @method static Result query(Query $query)
 * @method static string serialize(mixed $data, array $context = [])
 * @method static object deserialize(string $data, string $type, array $context = [])
 * @method static bool has(string $id)
 * @method static State get(string $id)
 * @method static State put(State $state)
 * @method static Query newQuery(string $type, Request $request = null)
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
        return 'stateful';
    }
}
