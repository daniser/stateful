<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use TTBooking\Stateful\Contracts\RepositoryFactory;
use TTBooking\Stateful\Contracts\StateRepository;

/**
 * @extends Support\Manager<StateRepository>
 */
class StorageManager extends Support\Manager implements RepositoryFactory, StateRepository
{
    protected string $selectorKey = 'stateful.store';

    public function has(string $id): bool
    {
        return $this->connection()->has($id);
    }

    public function get(string $id): State
    {
        return $this->connection()->get($id);
    }

    public function put(State $state): State
    {
        return $this->connection()->put($state);
    }

    /**
     * @param  array{stores: list<string|null>}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createAggregateDriver(array $config): Repositories\AggregateRepository
    {
        return $this->createDriver(Repositories\AggregateRepository::class, $config);
    }

    /**
     * @param  array{model: class-string<Models\State>}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createEloquentDriver(array $config): Repositories\EloquentRepository
    {
        return $this->createDriver(Repositories\EloquentRepository::class, $config);
    }

    /**
     * @param  array{table: string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDatabaseDriver(array $config): Repositories\DatabaseRepository
    {
        return $this->createDriver(Repositories\DatabaseRepository::class, $config);
    }

    /**
     * @param  array{path: string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createFilesystemDriver(array $config): Repositories\FilesystemRepository
    {
        return $this->createDriver(Repositories\FilesystemRepository::class, $config);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function createArrayDriver(): Repositories\ArrayRepository
    {
        return $this->createDriver(Repositories\ArrayRepository::class);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function createNullDriver(): Repositories\NullRepository
    {
        return $this->createDriver(Repositories\NullRepository::class);
    }

    /**
     * @template TStateRepository of StateRepository
     *
     * @param  class-string<TStateRepository>  $driver
     * @param  array<string, mixed>  $config
     * @return TStateRepository
     *
     * @throws BindingResolutionException
     */
    protected function createDriver(string $driver, array $config = [])
    {
        /** @var TStateRepository */
        return $this->container->make($driver, $config);
    }
}
