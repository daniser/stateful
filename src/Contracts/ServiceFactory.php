<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use InvalidArgumentException;

interface ServiceFactory
{
    /**
     * Get a service instance.
     *
     * @throws InvalidArgumentException
     */
    public function service(?string $name = null): Service;
}
