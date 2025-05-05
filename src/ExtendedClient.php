<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Pipeline\Pipeline;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\Result;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\Exceptions\ClientException;

class ExtendedClient implements Contracts\Client
{
    /**
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        protected Contracts\Client $client,
        protected Pipeline $pipeline,
        protected array $middleware = [],
    ) {}

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
