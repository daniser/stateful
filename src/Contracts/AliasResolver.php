<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;

interface AliasResolver
{
    /**
     * @return class-string<QueryPayload>
     *
     * @throws UnknownQueryTypeException
     */
    public function resolveQueryPayloadClass(string $alias): string;

    /**
     * @return class-string<ResultPayload>
     *
     * @throws UnknownQueryTypeException
     */
    public function resolveResultPayloadClass(string $alias): string;
}
