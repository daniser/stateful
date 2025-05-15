<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Casts\Query;
use TTBooking\Stateful\Casts\Result;
use TTBooking\Stateful\Contracts\Query as QueryContract;
use TTBooking\Stateful\Contracts\Result as ResultContract;

/**
 * @property string $id
 * @property string $service
 * @property string $type
 * @property QueryContract $query
 * @property ResultContract $result
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class State extends Model
{
    protected $table = 'stateful_state';

    protected $casts = [
        'query' => Query::class,
        'result' => Result::class,
    ];
}
