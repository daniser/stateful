<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\State as StatefulState;
use TTBooking\Stateful\StorageManager;

/**
 * @method static StateRepository connection(string $name = null)
 * @method static bool has(string $id)
 * @method static StatefulState get(string $id)
 * @method static StatefulState put(StatefulState $state)
 *
 * @see StorageManager
 */
class State extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'stateful-store';
    }
}
