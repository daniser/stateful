<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Illuminate\Support\Reflector;
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
}
