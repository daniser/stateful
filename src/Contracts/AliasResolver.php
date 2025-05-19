<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;

interface AliasResolver
{
    /**
     * @template TAlias of non-empty-string
     *
     * @phpstan-param TAlias $alias
     *
     * @return class-string<QueryPayload<TAlias>>
     *
     * @throws UnknownQueryTypeException
     */
    public function resolveAlias(string $alias): string;
}
