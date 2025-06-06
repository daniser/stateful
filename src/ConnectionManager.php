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
     * @param  array{uri: string, middleware?: list<class-string>}  $config
     * @return ExtendedClient<Client>
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config): ExtendedClient
    {
        /** @var list<class-string> $middleware */
        $middleware = Arr::pull($config, 'middleware', config('stateful.middleware', []));

        /** @var Client $client */
        $client = $this->container->make(Client::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'defaultContext' => $config,
        ]);

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
