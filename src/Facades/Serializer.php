<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\Stateful\Contracts\Serializer as SerializerContract;

/**
 * @method static SerializerContract serializer(string $name = null)
 * @method static string serialize(mixed $data, array $context = [])
 * @method static object deserialize(string $data, string $type, array $context = [])
 *
 * @see \TTBooking\Stateful\SerializerManager
 */
class Serializer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'stateful-serializer';
    }
}
