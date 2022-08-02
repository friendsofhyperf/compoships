<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Database\Eloquent;

use Awobaz\Compoships\Compoships;
use Hyperf\Database\Model\Model as Eloquent;

class Model extends Eloquent
{
    use Compoships;
}
