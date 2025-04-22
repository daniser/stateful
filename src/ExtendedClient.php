<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Contracts\Pipeline\Pipeline;

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

    public function query(Contracts\Query $query): Contracts\Result
    {
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
