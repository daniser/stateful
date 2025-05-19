<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

/**
 * @template TAlias of non-empty-string = non-empty-string
 * @template TResultPayload of ResultPayload = ResultPayload
 */
interface QueryPayload
{
    /**
     * @phpstan-return TAlias
     */
    public static function getAlias(): string;

    /**
     * @return class-string<TResultPayload>
     */
    public static function getResultPayloadType(): string;
}
