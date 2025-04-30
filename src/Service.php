<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;

class Service implements Contracts\Service
{
    public function __construct(
        protected Contracts\Serializer $serializer,
        protected Contracts\Client $client,
        protected Contracts\StateRepository $store,
    ) {}

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
}
