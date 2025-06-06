<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

interface StateRepository
{
    public function has(string $id): bool;

    /**
     * @throws StateNotFoundException
     */
    public function get(string $id): State;

    /**
     * @template TState of State
     *
     * @param  TState  $state
     * @return TState
     */
    public function put(State $state): State;
}
