<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use InvalidArgumentException;

interface SerializerFactory
{
    /**
     * Get a serializer instance.
     *
     * @throws InvalidArgumentException
     */
    public function serializer(?string $name = null): Serializer;
}
