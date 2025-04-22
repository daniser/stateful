<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Exceptions;

final class AggregateStateNotFoundException extends StateNotFoundException
{
    /** @var list<StateNotFoundException> */
    private array $exceptions = [];

    /**
     * @param  list<StateNotFoundException>  $exceptions
     */
    public static function withExceptions(string $message = '', array $exceptions = []): self
    {
        return (new self($message))->setExceptions($exceptions);
    }

    /**
     * @param  list<StateNotFoundException>  $exceptions
     */
    public function setExceptions(array $exceptions): self
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    /**
     * @return list<StateNotFoundException>
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
