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
use Hyperf\Database\Model\Model;

/**
 * @property string $contract_number
 * @property int $pickup_index
 * @property string $days
 * @property string $pickup_time
 * @property PickupPoint $pickupPoint
 *
 * @mixin \Hyperf\Database\Model\Builder
 */
class PickupTime extends Model
{
    use Compoships;

    public function pickupPoint()
    {
        return $this->belongsTo(PickupPoint::class, ['contract_number', 'pickup_index'], [
            'contract_number',
            'pickup_index',
        ]);
    }
}
