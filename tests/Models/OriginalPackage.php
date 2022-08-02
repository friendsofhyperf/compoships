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
 * @property int $id
 * @property string $name
 * @property int $allocation_id
 * @property string $pcid
 * @property Allocation $allocation
 * @property ProductCode $productCode
 * @property ProductCode $productCode2
 *
 * @mixin \Hyperf\Database\Query\Builder
 */
class OriginalPackage extends Model
{
    use Compoships;

    public $timestamps = false;

    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo
     */
    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo
     */
    public function productCode()
    {
        return $this->belongsTo(ProductCode::class, 'pcid', 'pcid');
    }

    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo
     */
    public function productCode2()
    {
        return $this->belongsTo(ProductCode::class, ['pcid'], ['pcid']);
    }
}
