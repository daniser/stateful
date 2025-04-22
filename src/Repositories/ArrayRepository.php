<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class ArrayRepository implements StateRepository
{
    /** @var array<string, State> */
    protected array $states = [];

    public function has(string $id): bool
    {
        return isset($this->states[$id]);
    }

    public function get(string $id): State
    {
        return $this->states[$id] ?? throw new StateNotFoundException("State [$id] not found");
    }

    public function put(State $state): State
    {
        return $this->states[$state->id] = $state;
    }
}
