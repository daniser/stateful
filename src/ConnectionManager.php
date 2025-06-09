<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pipeline\Pipeline;
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

    /**
     * @param  array{middleware?: list<class-string>}  $config
     * @return ExtendedClient<covariant Contracts\Client>
     */
    protected function createInstance(array $config, string $name, string $driver): ExtendedClient
    {
        /** @var list<class-string> $middleware */
        $middleware = Arr::pull($config, 'middleware', config('stateful.middleware', []));

        // @phpstan-ignore argument.type
        $client = parent::createInstance($config, $name, $driver);

        return $this->decorateInstance($client, $middleware);
    }

    /**
     * @template TClient of Contracts\Client
     *
     * @param  TClient  $client
     * @param  list<class-string>  $middleware
     * @return (TClient is ExtendedClient<Contracts\Client> ? TClient : ExtendedClient<TClient>)
     */
    protected function decorateInstance(Contracts\Client $client, array $middleware): ExtendedClient
    {
        /** @var (TClient is ExtendedClient<Contracts\Client> ? TClient : ExtendedClient<TClient>) */
        return $client instanceof ExtendedClient ?
            $client : new ExtendedClient($client, new Pipeline($this->container), $middleware);
    }
}
