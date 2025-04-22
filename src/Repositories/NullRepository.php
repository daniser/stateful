<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class NullRepository implements StateRepository
{
    public function has(string $id): bool
    {
        return false;
    }

    public function get(string $id): never
    {
        throw new StateNotFoundException('Null repository is always empty');
    }

    public function put(State $state): State
    {
        return $state;
    }
}
