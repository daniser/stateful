<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Support;

use OuterIterator;
use RecursiveIterator;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements OuterIterator<TKey, TValue>
 * @implements RecursiveIterator<TKey, TValue>
 */
class RecursivePathIterator implements OuterIterator, RecursiveIterator
{
    /**
     * @param  RecursiveIterator<TKey, TValue>  $iterator
     */
    public function __construct(protected RecursiveIterator $iterator, protected string $prefix = '') {}

    /**
     * @return RecursiveIterator<TKey, TValue>
     */
    public function getInnerIterator(): RecursiveIterator
    {
        return $this->iterator;
    }

    /**
     * @return self<TKey, TValue>|null
     */
    public function getChildren(): ?self
    {
        /** @var RecursiveIterator<TKey, TValue>|null $children */
        $children = $this->iterator->getChildren();

        return isset($children) ? new self($children, $this->path().'.') : null;
    }

    public function hasChildren(): bool
    {
        return $this->iterator->hasChildren();
    }

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function key(): mixed
    {
        return $this->iterator->key();
    }

    public function path(): string
    {
        return $this->prefix.str_replace('.', '\\.', (string) $this->iterator->key());
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }
}
