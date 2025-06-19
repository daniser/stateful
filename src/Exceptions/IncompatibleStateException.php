<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Exceptions;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResultPayload;
use TTBooking\Stateful\State;
use TypeError;

class IncompatibleStateException extends InvalidArgumentException implements RequestExceptionInterface
{
    public static function fromTypeError(TypeError $e, string $type, ?State $state = null): ?self
    {
        if (
            preg_match('/must be of type ([a-zA-Z0-9\\\\]+), ([a-zA-Z0-9\\\\]+) given/', $e->getMessage(), $matches) &&
            (
                ($qm = is_subclass_of($matches[1], QueryPayload::class) && is_subclass_of($matches[2], QueryPayload::class)) ||
                is_subclass_of($matches[1], ResultPayload::class) && is_subclass_of($matches[2], ResultPayload::class)
            )
        ) {
            $alias = $state?->query->getPayload()::getAlias() ?? 'unknown';
            $desc = $qm ? '' : ' result of';

            return new self("Incompatible$desc [$alias] query given for [$type] query.", $e->getCode(), $e);
        }

        return null;
    }
}
