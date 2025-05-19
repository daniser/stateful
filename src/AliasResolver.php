<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Support\Arr;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;

class AliasResolver implements Contracts\AliasResolver
{
    /** @var array<string, class-string<QueryPayload>> */
    private array $map;

    /**
     * @param  array<array-key, class-string<QueryPayload>>  $queryPayloadClasses
     */
    public function __construct(array $queryPayloadClasses)
    {
        $this->map = static::buildMap($queryPayloadClasses);
    }

    public function resolveAlias(string $alias): string
    {
        return $this->map[$alias] ?? throw new UnknownQueryTypeException("Unknown query type [$alias].");
    }

    /**
     * @param  array<array-key, class-string<QueryPayload>>  $queryPayloadClasses
     * @return array<string, class-string<QueryPayload>>
     */
    protected static function buildMap(array $queryPayloadClasses): array
    {
        if (! array_is_list($queryPayloadClasses)) {
            /** @var array<string, class-string<QueryPayload>> */
            return $queryPayloadClasses;
        }

        /** @var array<string, class-string<QueryPayload>> */
        return Arr::mapWithKeys(
            $queryPayloadClasses,
            static fn ($class) => [$class::getAlias() => $class]
        );
    }
}
