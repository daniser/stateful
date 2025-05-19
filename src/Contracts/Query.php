<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

/**
 * @template TQueryPayload of QueryPayload = QueryPayload
 */
interface Query
{
    /**
     * @return $this
     */
    public function withBaseUri(string $baseUri): static;

    public function getBaseUri(): ?string;

    /**
     * @param  array<string, mixed>  $context
     * @return $this
     */
    public function withContext(array $context): static;

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    /**
     * @phpstan-param TQueryPayload $payload
     *
     * @return $this
     */
    public function withPayload(QueryPayload $payload): static;

    /**
     * @phpstan-return TQueryPayload
     */
    public function getPayload(): QueryPayload;

    public function getEndpoint(): string;

    public function getMethod(): string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
}
