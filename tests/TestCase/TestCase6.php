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

use PHPUnit\Framework\Assert;

/**
 * PHP 7.0 | 7.1
 * PHPUnit 6.x.
 *
 * @see https://github.com/sebastianbergmann/phpunit/issues/3368
 * @see https://phpunit.de/supported-versions.html
 */
abstract class TestCase extends AbstractTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->setupDatabase();
    }

    /**
     * Asserts that a variable is of type array.
     * @param mixed $actual
     */
    public static function assertIsArray($actual, string $message = '')
    {
        Assert::assertInternalType('array', $actual, $message);
    }
}
