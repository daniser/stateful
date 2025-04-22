<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\Exceptions\ClientException;

interface Client
{
    /**
     * @template TResult of Result
     * @template TQuery of Query<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @phpstan-return TResult
     *
     * @throws ClientException
     */
    public function query(Query $query): Result;
}
