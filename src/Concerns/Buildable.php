<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Concerns;

use function TTBooking\Stateful\{ complete, entity };

/**
 * @method static bool isComplete()
 */
trait Buildable
{
    /**
     * @param  list<mixed>  $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return entity(static::class)->$name(...$arguments);
    }

    public function isComplete(): bool
    {
        return complete($this);
    }
}
