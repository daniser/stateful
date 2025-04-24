<?php

declare(strict_types=1);

namespace TTBooking\Stateful;

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
     * @param  list<mixed>  $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): static
    {
        $this->payload->$name(...$arguments);

        return $this;
    }
}
