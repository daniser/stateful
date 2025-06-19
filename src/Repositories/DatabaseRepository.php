<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Database\ConnectionInterface;
use TTBooking\Stateful\Contracts\AliasResolver;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\Serializer;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class DatabaseRepository implements StateRepository
{
    public function __construct(
        protected ConnectionInterface $connection,
        protected AliasResolver $aliasResolver,
        protected Serializer $serializer,
        protected string $table = 'stateful_state',
    ) {}

    public function has(string $id): bool
    {
        return $this->connection->table($this->table)->where('id', $id)->exists();
    }

    public function get(string $id): State
    {
        $record = $this->connection->table($this->table)->where('id', $id)->first();

        if (! $record) {
            throw new StateNotFoundException("State [$id] not found");
        }

        /**
         * @var \stdClass&object{
         *     id: string,
         *     service: non-empty-string,
         *     type: non-empty-string,
         *     query: string,
         *     result: string,
         *     meta: string,
         * } $state
         */
        $state = (object) $record;

        $queryPayloadClass = $this->aliasResolver->resolveAlias($state->type);

        /** @var Query $query */
        $query = $this->serializer->deserialize($state->query, $queryPayloadClass);

        /** @var Result $result */
        $result = $this->serializer->deserialize($state->result, $queryPayloadClass::getResultPayloadType());

        /** @var array{parent_id?: string} $meta */
        $meta = json_decode($state->meta, true);

        return new State(
            id: $state->id,
            query: $query,
            result: $result,
            service: $state->service,
            parentId: $meta['parent_id'] ?? null,
        );
    }

    public function put(State $state): State
    {
        $this->connection->table($this->table)->insert([
            'id' => $state->id,
            'base_uri' => $state->query->getBaseUri(),
            'service' => $state->service,
            'type' => get_class($state->query),
            'query' => $this->serializer->serialize($state->query),
            'result' => $this->serializer->serialize($state->result),
            'meta' => ['parent_id' => $state->parentId],
        ]);

        return $state;
    }
}
