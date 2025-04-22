<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use InvalidArgumentException;

interface ServiceFactory
{
    /**
     * Get a connection instance.
     *
     * @throws InvalidArgumentException
     */
    public function service(?string $name = null): Service;
}
