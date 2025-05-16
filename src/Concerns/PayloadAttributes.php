<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Exception;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use TTBooking\Stateful\Attributes;
use TTBooking\Stateful\Contracts\Query;
use TTBooking\Stateful\Contracts\ResultPayload;

/**
 * @template TResultPayload of ResultPayload
 *
 * @phpstan-require-implements Query
 */
trait PayloadAttributes
{
    public static function getAliasFor(string $payloadClass): string
    {
        return Reflector::getClassAttribute($payloadClass, Attributes\Alias::class)->alias
            ?? Str::snake(class_basename($payloadClass));
    }

    public function getAlias(): string
    {
        return static::getAliasFor(get_class($this->getPayload()));
    }

    public function getEndpoint(): string
    {
        return Reflector::getClassAttribute($this->getPayload(), Attributes\Endpoint::class)->endpoint ?? '';
    }

    public function getMethod(): string
    {
        return Reflector::getClassAttribute($this->getPayload(), Attributes\Method::class)->method ?? 'POST';
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return Reflector::getClassAttribute($this->getPayload(), Attributes\Headers::class)->headers ?? [];
    }

    /**
     * @throws Exception
     */
    public static function getResultTypeFor(string $payloadClass): string
    {
        return Reflector::getClassAttribute($payloadClass, Attributes\ResultType::class)->type
            ?? throw new Exception('ResultType attribute not defined.');
    }

    /**
     * @throws Exception
     */
    public function getResultType(): string
    {
        /** @var class-string<TResultPayload> */
        return static::getResultTypeFor(get_class($this->getPayload()));
    }
}
