<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Tests\TestCase;

use Awobaz\Compoships\Tests\Migration;
use Carbon\Carbon;
use Hyperf\Database\Capsule\Manager as Capsule;
use Hyperf\Database\Model\Model;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    protected function setupDatabase()
    {
        Model::reguard();
        Carbon::setTestNow('2020-10-29 23:59:59');

        $capsuleManager = new Capsule();
        $capsuleManager->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsuleManager->setAsGlobal();
        $capsuleManager->bootEloquent();

        (new Migration())->up();
    }
}
