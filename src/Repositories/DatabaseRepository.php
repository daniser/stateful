<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Database\ConnectionInterface;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class DatabaseRepository implements StateRepository
{
    public function __construct(
        protected ConnectionInterface $connection,
        protected string $table = 'stateful_state',
    ) {}

    public function has(string $id): bool
    {
        return $this->connection->table($this->table)->where('id', $id)->exists();
    }

    public function get(string $id): State
    {
        $record = $this->connection->table($this->table)->where('id', $id)->first();

        if (! $record) {
            throw new StateNotFoundException("State [$id] not found");
        }

        $record = (array) $record;

        // TODO

        throw new StateNotFoundException;
    }

    public function put(State $state): State
    {
        // TODO: Implement put() method.

        return $state;
    }
}
