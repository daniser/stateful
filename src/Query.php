<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use ArrayAccess;
use TTBooking\Stateful\Concerns\DelegatesToPayload;
use TTBooking\Stateful\Concerns\PayloadAttributes;

/**
 * @template TPayload of object
 * @template TResult of Contracts\Result
 *
 * @implements ArrayAccess<string, mixed>
 * @implements Contracts\Query<TPayload, TResult>
 *
 * @mixin TPayload
 */
class Query implements ArrayAccess, Contracts\Query
{
    /** @use PayloadAttributes<TResult> */
    use DelegatesToPayload, PayloadAttributes;

    protected ?string $baseUri = null;

    /** @var array<string, mixed> */
    protected array $context = [];

    /**
     * @phpstan-param TPayload $payload
     */
    public function __construct(protected object $payload) {}

    public function withBaseUri(string $baseUri): static
    {
        $this->baseUri ??= $baseUri;

        return $this;
    }

    public function getBaseUri(): ?string
    {
        return $this->baseUri;
    }

    public function withContext(array $context): static
    {
        $this->context = $context + $this->context;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function withPayload(object $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPayload(): object
    {
        return $this->payload;
    }

    /**
     * Force a clone of the underlying payload when cloning.
     */
    public function __clone(): void
    {
        $this->payload = clone $this->payload;
    }
}
