<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Contracts;

interface Service extends Client, Serializer, StateRepository {}
