<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use InvalidArgumentException;

/**
 * @template-covariant TConnection of object
 */
interface Factory
{
    /**
     * Get a connection instance.
     *
     * @return TConnection
     *
     * @throws InvalidArgumentException
     */
    public function connection(?string $name = null);

    /**
     * Create a new connection.
     *
     * @param  array<string, mixed>  $config
     * @return TConnection
     */
    public function makeConnection(array $config, ?string $name = null);

    /**
     * Get all of the created connections.
     *
     * @return array<string, TConnection>
     */
    public function getConnections(): array;
}
