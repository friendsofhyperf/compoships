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

/**
 * @property string $contract_number
 * @property int $pickup_index
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property PickupTime[] $pickupTimes
 *
 * @mixin \Hyperf\Database\Query\Builder
 */
class PickupPoint extends Model
{
    use Compoships;

    // NOTE: we need this because Laravel 7 uses Carbon's method toJSON() instead of toDateTimeString()
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function pickupTimes()
    {
        return $this->hasMany(PickupTime::class, ['contract_number', 'pickup_index'], [
            'contract_number',
            'pickup_index',
        ]);
    }
}
