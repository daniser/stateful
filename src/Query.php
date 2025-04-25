<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;

/**
 * @template TPayload of object
 * @template TResult of Contracts\Result
 *
 * @implements Contracts\Query<TPayload, TResult>
 *
 * @mixin TPayload
 */
class Query implements Contracts\Query
{
    /** @use Concerns\PayloadAttributes<TResult> */
    use Concerns\PayloadAttributes;

    use ForwardsCalls, Macroable {
        Macroable::__call as macroCall;
    }

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
     * Determine if an attribute exists on the payload.
     */
    public function __isset(string $key): bool
    {
        return isset($this->payload->$key);
    }

    /**
     * Unset an attribute on the payload.
     */
    public function __unset(string $key): void
    {
        unset($this->payload->$key);
    }

    /**
     * Dynamically get properties from the underlying payload.
     */
    public function __get(string $key): mixed
    {
        return $this->payload->$key;
    }

    /**
     * Dynamically pass method calls to the underlying payload.
     *
     * @param  string  $method
     * @param  list<mixed>  $parameters
     */
    public function __call($method, $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->forwardDecoratedCallTo($this->payload, $method, $parameters);
    }

    /**
     * Force a clone of the underlying payload when cloning.
     */
    public function __clone(): void
    {
        $this->payload = clone $this->payload;
    }
}
