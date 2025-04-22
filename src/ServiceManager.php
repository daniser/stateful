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
     * @param  array{connection: string, store: string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config): Service
    {
        return new Service(
            $this->container['stateful-client']->connection($config['connection'] ?? null),
            $this->container['stateful-store']->connection($config['store'] ?? null),
        );
    }
}
