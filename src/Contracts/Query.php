<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

/**
 * @template TResult of Result
 */
interface Query
{
    /**
     * @return $this
     */
    public function withBaseUri(string $baseUri): static;

    public function getBaseUri(): string;

    /**
     * @param  array<string, mixed>  $context
     *
     * @return $this
     */
    public function withContext(array $context): static;

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * @return $this
     */
    public function withPayload(object $payload): static;

    public function getPayload(): object;

    public function getAlias(): string;

    public function getEndpoint(): string;

    public function getMethod(): string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * @return class-string<TResult>
     */
    public function getResultType(): string;
}
