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

/**
 * Supports latest versions.
 *
 * PHP 7.2, 7.3, 7.4
 * PHPUnit 7.x, 8.x
 *
 * @see https://phpunit.de/supported-versions.html
 */
abstract class TestCase extends AbstractTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->setupDatabase();
    }
}
