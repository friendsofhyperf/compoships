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
 * @property string $pcid
 * @property string $code
 *
 * @mixin \Hyperf\Database\Model\Builder
 */
class ProductCode extends Model
{
    use Compoships;

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'product_codes';
}
