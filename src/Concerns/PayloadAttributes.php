<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Exception;
use Illuminate\Support\Str;
use TTBooking\Stateful\Attributes;
use TTBooking\Stateful\Contracts\ResultPayload;
use function TTBooking\Stateful\class_attribute;

/**
 * @template TResultPayload of ResultPayload
 */
trait PayloadAttributes
{
    public function getAlias(): string
    {
        return class_attribute($this->getPayload(), Attributes\Alias::class)->alias
            ?? Str::snake(class_basename(static::class));
    }

    public function getEndpoint(): string
    {
        return class_attribute($this->getPayload(), Attributes\Endpoint::class)->endpoint ?? '';
    }

    public function getMethod(): string
    {
        return class_attribute($this->getPayload(), Attributes\Method::class)->method ?? 'POST';
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return class_attribute($this->getPayload(), Attributes\Headers::class)->headers ?? [];
    }

    /**
     * @throws Exception
     */
    public function getResultType(): string
    {
        /** @var class-string<TResultPayload> */
        return class_attribute($this->getPayload(), Attributes\ResultType::class)->type
            ?? throw new Exception('ResultType attribute not defined.');
    }
}
