<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use BadMethodCallException;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;

trait DelegatesToPayload
{
    use ForwardsCalls, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * Determine if the given property exists.
     *
     * @param  string  $offset
     */
    public function offsetExists($offset): bool
    {
        throw new BadMethodCallException('Method not implemented.');
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     */
    public function offsetGet($offset): mixed
    {
        return data_get($this->payload, $offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  string  $offset
     */
    public function offsetSet($offset, $value): void
    {
        data_set($this->payload, $offset, $value); // @phpstan-ignore assign.propertyType
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  string  $offset
     */
    public function offsetUnset($offset): void
    {
        data_forget($this->payload, $offset); // @phpstan-ignore assign.propertyType
    }

    /**
     * Determine if a property exists on the payload.
     */
    public function __isset(string $key): bool
    {
        return isset($this->payload->$key);
    }

    /**
     * Unset a property on the payload.
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
}
