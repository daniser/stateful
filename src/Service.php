<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;
use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;
use TTBooking\Stateful\Query as ConcreteQuery;

class Service implements Contracts\Service
{
    /** @var array<string, class-string<QueryPayload>> */
    private array $queryMap;

    /**
     * @param  array<array-key, class-string<QueryPayload>>  $queryPayloadClasses
     */
    public function __construct(
        protected Contracts\Serializer $serializer,
        protected Contracts\Client $client,
        protected Contracts\StateRepository $store,
        protected Container $container,
        array $queryPayloadClasses = [],
    ) {
        $this->queryMap = self::buildQueryMap($queryPayloadClasses);
    }

    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer->serialize($data, $context);
    }

    public function deserialize(string $data, string $type, array $context = []): object
    {
        return $this->serializer->deserialize($data, $type, $context);
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

    public function resolveQueryPayloadClass(string $alias): string
    {
        return $this->queryMap[$alias] ?? throw new UnknownQueryTypeException("Unknown query type [$alias].");
    }

    public function resolveResultPayloadClass(string $alias): string
    {
        return ConcreteQuery::getResultTypeFor($this->resolveQueryPayloadClass($alias));
    }

    /**
     * @param  array<array-key, class-string<QueryPayload>>  $queryPayloadClasses
     * @return array<string, class-string<QueryPayload>>
     */
    private static function buildQueryMap(array $queryPayloadClasses): array
    {
        if (! array_is_list($queryPayloadClasses)) {
            /** @var array<string, class-string<QueryPayload>> */
            return $queryPayloadClasses;
        }

        /** @var array<string, class-string<QueryPayload>> */
        return Arr::mapWithKeys(
            $queryPayloadClasses,
            static fn (string $class) => [ConcreteQuery::getAliasFor($class) => $class]
        );
    }
}
