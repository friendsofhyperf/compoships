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

use Carbon\Carbon;
use Hyperf\Database\Model\Model;

/**
 * @property int $id
 * @property int $booking_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin \Hyperf\Database\Model\Builder
 */
class Space extends Model
{
    // NOTE: we need this because Laravel 7 uses Carbon's method toJSON() instead of toDateTimeString()
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
