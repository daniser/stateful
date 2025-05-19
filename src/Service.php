<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;
use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;

class Service implements Contracts\Service
{
    public function __construct(
        protected Contracts\Serializer $serializer,
        protected Contracts\AliasResolver $aliasResolver,
        protected Contracts\Client $client,
        protected Contracts\StateRepository $store,
        protected Container $container,
    ) {}

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer->serialize($data, $context);
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        return $this->serializer->deserialize($data, $type, $context);
    }

    public function resolveAlias(string $alias): string
    {
        return $this->aliasResolver->resolveAlias($alias);
    }

    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @return Result<TResultPayload>
     *
     * @throws ClientException
     */
    public function query(Query $query): Result
    {
        return $this->client->query($query);
    }

    public function has(string $id): bool
    {
        return $this->store->has($id);
    }

    public function get(string $id): State
    {
        return $this->store->get($id);
    }

    public function put(State $state): State
    {
        return $this->store->put($state);
    }

    public function newQuery(string $type, ?Request $request = null): Query
    {
        method_exists($this, $method = 'new'.Str::studly($type).'Query')
            or throw new UnknownQueryTypeException("Unknown query type [$type].");

        /** @var Query */
        return $this->container->call($this->$method(...));
    }
}
