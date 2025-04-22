<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use Exception;
use TTBooking\Stateful\Attributes;
use TTBooking\Stateful\Contracts\Result;
use function TTBooking\Stateful\{ attribute, snake };

/**
 * @template TResult of Result
 */
trait PayloadAttributes
{
    public function getAlias(): string
    {
        return attribute($this->getPayload(), Attributes\Alias::class)->alias
            ?? snake(basename(str_replace('\\', '/', static::class)));
    }

    public function getEndpoint(): string
    {
        return attribute($this->getPayload(), Attributes\Endpoint::class)->endpoint ?? '';
    }

    public function getMethod(): string
    {
        return attribute($this->getPayload(), Attributes\Method::class)->method ?? 'POST';
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return attribute($this->getPayload(), Attributes\Headers::class)->headers ?? [];
    }

    /**
     * @throws Exception
     */
    public function getResultType(): string
    {
        /** @var class-string<TResult> */
        return attribute($this->getPayload(), Attributes\ResultType::class)->type
            ?? throw new Exception('ResultType attribute not defined.');
    }
}
