<?php

declare(strict_types=1);

namespace TTBooking\Stateful\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TTBooking\Stateful\Casts\Query;
use TTBooking\Stateful\Casts\Result;
use TTBooking\Stateful\Contracts\QueryPayload;
use TTBooking\Stateful\Contracts\ResultPayload;

/**
 * @property string $id
 * @property string $type
 * @property QueryPayload $query
 * @property ResultPayload $result
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
