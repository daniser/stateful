<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Repositories;

use Illuminate\Database\ConnectionInterface;
use TTBooking\Stateful\Concerns\HasSerializer;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\Serializer;
use TTBooking\Stateful\Contracts\SerializesData;
use TTBooking\Stateful\Contracts\StateRepository;
use TTBooking\Stateful\Exceptions\StateNotFoundException;
use TTBooking\Stateful\State;

class DatabaseRepository implements SerializesData, StateRepository
{
    use HasSerializer;

    public function __construct(
        protected ConnectionInterface $connection,
        Serializer $serializer,
        protected string $table = 'stateful_state',
    ) {
        $this->setSerializer($serializer);
    }

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
         *     type: class-string<QueryPayload>,
         *     query: string,
         *     result: string,
         *     meta: string,
         * } $state
         */
        $state = (object) $record;

        /** @var Query<Result> $query */
        $query = $this->serializer->deserialize($state->query, $state->type);

        /** @var Result $result */
        $result = $this->serializer->deserialize($state->result, $query->getResultType());

        return new State(
            id: $state->id,
            query: $query,
            result: $result,
            parentId: $state->meta['parent_id'],
        );
    }

    public function put(State $state): State
    {
        $this->connection->table($this->table)->insert([
            'id' => $state->id,
            'base_uri' => $state->query->getBaseUri(),
            'type' => get_class($state->query),
            'query' => $this->serializer->serialize($state->query),
            'result' => $this->serializer->serialize($state->result),
            'meta' => ['parent_id' => $state->parentId],
        ]);

        return $state;
    }
}
