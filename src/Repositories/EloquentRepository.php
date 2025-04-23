<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use TTBooking\Stateful\Concerns\HasSerializer;
use TTBooking\Stateful\Contracts\SerializesData;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\Models\State as Model;
use TTBooking\Stateful\State;

class EloquentRepository implements SerializesData, StateRepository
{
    use HasSerializer;

    protected Model $model;

    public function __construct(Model|string $model = Model::class)
    {
        $this->model = is_string($model) ? new $model : $model;
    }

    public function has(string $id): bool
    {
        return $this->model->newQuery()->whereKey($id)->exists();
    }

    public function get(string $id): State
    {
        // TODO: Implement get() method.

        throw new StateNotFoundException;
    }

    public function put(State $state): State
    {
        // TODO: Implement put() method.

        return $state;
    }
}
