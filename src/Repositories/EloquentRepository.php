<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\Models\State as Model;
use TTBooking\Stateful\State;

class EloquentRepository implements StateRepository
{
    protected Model $model;

    /**
     * @param  Model|class-string<Model>  $model
     */
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
        try {
            $model = $this->model->newQuery()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new StateNotFoundException("State [$id] not found.", $e->getCode(), $e);
        }

        return new State($id, $model->query, $model->result, $model->service);
    }

    public function put(State $state): State
    {
        $this->model->newQuery()->forceCreate([
            'id' => $state->id,
            'service' => $state->service,
            'query' => $state->query,
            'result' => $state->result,
        ]);

        return $state;
    }
}
