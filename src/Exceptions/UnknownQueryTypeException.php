<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;

class UnknownQueryTypeException extends RuntimeException implements RequestExceptionInterface {}
