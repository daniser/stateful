<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\Exceptions\ClientException;

interface Client
{
    /**
     * @template TResultPayload of ResultPayload
     * @template TQueryPayload of QueryPayload<non-empty-string, TResultPayload>
     *
     * @param  Query<TQueryPayload>  $query
     * @return Result<TResultPayload>
     *
     * @throws ClientException
     */
    public function query(Query $query): Result;
}
