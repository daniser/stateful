<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;

/**
 * @extends Support\Manager<Contracts\Client>
 */
class ConnectionManager extends Support\Manager implements Contracts\Client, Contracts\ClientFactory
{
    protected string $selectorKey = 'stateful.connection';

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<non-empty-string, TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @return Result<TResultPayload>
     *
     * @throws ClientException
     */
    public function query(Query $query): Result
    {
        return $this->connection()->query($query);
    }

    /**
     * @param  array{uri: string}  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config): Client
    {
        /** @var Client */
        return $this->container->make(Client::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'defaultContext' => $config,
        ]);
    }
}
