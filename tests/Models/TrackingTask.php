<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Tests\Models;

use Awobaz\Compoships\Compoships;
use Carbon\Carbon;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $vehicle_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Allocation $allocation
 * @property TrackingTask[] $subTasks
 *
 * @mixin \Hyperf\Database\Model\Builder
 */
class TrackingTask extends Model
{
    use SoftDeletes;
    use Compoships;

    // NOTE: we need this because Laravel 7 uses Carbon's method toJSON() instead of toDateTimeString()
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class, ['booking_id', 'vehicle_id'], ['booking_id', 'vehicle_id']);
    }

    public function subTasks()
    {
        return $this->hasMany(TrackingTask::class, ['booking_id', 'vehicle_id'], ['booking_id', 'vehicle_id']);
    }
}
