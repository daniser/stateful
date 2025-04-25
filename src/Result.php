<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use ArrayAccess;
use TTBooking\Stateful\Concerns\DelegatesToPayload;

/**
 * @template TPayload of object
 *
 * @implements ArrayAccess<string, mixed>
 * @implements Contracts\Result<TPayload>
 *
 * @mixin TPayload
 */
class Result implements ArrayAccess, Contracts\Result
{
    use DelegatesToPayload;

    /** @var array<string, mixed> */
    protected array $context = [];

    /**
     * @phpstan-param TPayload $payload
     */
    public function __construct(protected object $payload) {}

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
}
