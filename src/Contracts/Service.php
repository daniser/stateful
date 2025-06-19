<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

use Illuminate\Http\Request;
use TTBooking\Stateful\Exceptions\UnknownQueryTypeException;
use TTBooking\Stateful\State;

interface Service extends AliasResolver, Client, Serializer, StateRepository
{
    /**
     * @throws UnknownQueryTypeException
     */
    public function newQuery(string $type, ?State $state = null, ?Request $request = null): Query;
}
