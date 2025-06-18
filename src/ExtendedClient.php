<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Pipeline\Pipeline;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;

/**
 * @template TClient of Contracts\Client
 *
 * @mixin TClient
 */
class ExtendedClient implements Contracts\Client
{
    /**
     * @param  TClient  $client
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        protected Contracts\Client $client,
        protected Pipeline $pipeline,
        protected array $middleware = [],
    ) {}

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
        /** @var Result<TResultPayload> */
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->query(...));
    }

    /**
     * @param  list<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }
}
