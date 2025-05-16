<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResultPayload;
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

    public function resolveQueryPayloadClass(string $alias): string
    {
        return $this->map[$alias] ?? throw new UnknownQueryTypeException("Unknown query type [$alias].");
    }

    public function resolveResultPayloadClass(string $alias): string
    {
        return static::getResultTypeFor($this->resolveQueryPayloadClass($alias));
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
            static fn (string $class) => [static::getAliasFor($class) => $class]
        );
    }

    /**
     * @param  class-string<QueryPayload>  $payloadClass
     */
    protected static function getAliasFor(string $payloadClass): string
    {
        return Reflector::getClassAttribute($payloadClass, Attributes\Alias::class)->alias
            ?? Str::snake(class_basename($payloadClass));
    }

    /**
     * @template TResultPayload of ResultPayload
     *
     * @param  class-string<QueryPayload<TResultPayload>>  $payloadClass
     * @return class-string<TResultPayload>
     */
    protected static function getResultTypeFor(string $payloadClass): string
    {
        /** @var class-string<TResultPayload> */
        return Reflector::getClassAttribute($payloadClass, Attributes\ResultType::class)->type
            ?? throw new Exception('ResultType attribute not defined.');
    }
}
