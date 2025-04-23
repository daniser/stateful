<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Exceptions\ClientException;

/**
 * @extends Support\Manager<Contracts\Service>
 */
class ServiceManager extends Support\Manager implements Contracts\Service, Contracts\ServiceFactory
{
    protected string $selectorKey = 'stateful.service';

    /**
     * @template TResult of Result
     * @template TQuery of Query<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @phpstan-return TResult
     *
     * @throws ClientException
     */
    public function query(Query $query): Result
    {
        return $this->service()->query($query);
    }

    public function has(string $id): bool
    {
        return $this->service()->has($id);
    }

    public function get(string $id): State
    {
        return $this->service()->get($id);
    }

    public function put(State $state): State
    {
        return $this->service()->put($state);
    }

    public function service(?string $name = null): Contracts\Service
    {
        return $this->connection($name);
    }

    /**
     * @param  array{connection: array<string, mixed>|string|null, store: array<string, mixed>|string|null}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config, string $name): Service
    {
        return new Service(
            $this->resolveDeps(Contracts\ClientFactory::class, $config['connection'] ?? null, "$name.connection"),
            $this->resolveDeps(Contracts\RepositoryFactory::class, $config['store'] ?? null, "$name.store"),
        );
    }

    /**
     * @template TConnection of object
     *
     * @param  class-string<Contracts\Factory<TConnection>>  $factory
     * @param  array<string, mixed>|string|null  $config
     * @return TConnection
     *
     * @throws BindingResolutionException
     */
    protected function resolveDeps(string $factory, array|string|null $config, ?string $name = null)
    {
        /** @var Contracts\Factory<TConnection> $factory */
        $factory = $this->container->make($factory);

        return is_array($config) ? $factory->makeConnection($config, $name) : $factory->connection($config);
    }
}
