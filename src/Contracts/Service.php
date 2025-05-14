<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use Illuminate\Http\Request;
use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;

interface Service extends Client, Serializer, StateRepository
{
    /**
     * @throws UnknownQueryTypeException
     */
    public function newQuery(string $type, ?Request $request = null): Query;
}
