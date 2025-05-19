<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Exception;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use TTBooking\Stateful\Attributes\Alias;
use TTBooking\Stateful\Attributes\ResultType;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResultPayload;

/**
 * @template TAlias of non-empty-string
 * @template TResultPayload of ResultPayload = ResultPayload
 *
 * @phpstan-require-implements QueryPayload<TAlias, TResultPayload>
 */
trait Attributes
{
    /**
     * @phpstan-return TAlias
     */
    public static function getAlias(): string
    {
        return Reflector::getClassAttribute(static::class, Alias::class)->alias
            ?? Str::snake(class_basename(static::class));
    }

    /**
     * @return class-string<TResultPayload>
     */
    public static function getResultPayloadType(): string
    {
        return Reflector::getClassAttribute(static::class, ResultType::class)->type
            ?? throw new Exception('ResultType attribute not defined.');
    }
}
