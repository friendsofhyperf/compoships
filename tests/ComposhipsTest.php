<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Tests;

use Awobaz\Compoships\Tests\Models\Allocation;
use Awobaz\Compoships\Tests\Models\PickupPoint;
use Awobaz\Compoships\Tests\Models\PickupTime;
use Awobaz\Compoships\Tests\Models\Space;
use Awobaz\Compoships\Tests\Models\TrackingTask;
use Awobaz\Compoships\Tests\Models\User;
use Awobaz\Compoships\Tests\TestCase\TestCase;
use Hyperf\Database\Model\Model;

/**
 * @covers \Awobaz\Compoships\Compoships
 * @covers \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo
 * @covers \Awobaz\Compoships\Database\Eloquent\Relations\HasMany
 * @covers \Awobaz\Compoships\Database\Query\Builder
 *
 * @internal
 */
class ComposhipsTest extends TestCase
{
    /**
     * Test the save method on a relationship.
     */
    public function testSave()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = 1;
        $allocation->vehicle_id = 1;
        $allocation->save();

        $allocation->trackingTasks()
            ->save(new TrackingTask());

        $this->assertNotNull($allocation->trackingTasks);
        $this->assertInstanceOf(Allocation::class, $allocation->trackingTasks->first()->allocation);
    }

    /**
     * @covers \Awobaz\Compoships\Database\Eloquent\Relations\HasOne
     */
    public function testSaveModelNotUsingComposhipsOnHasOne()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = 1;
        $allocation->vehicle_id = 1;
        $allocation->save();

        $allocation->space()
            ->save(new Space());

        $this->assertNotNull($allocation->space);
    }

    /**
     * Test the save method on a relationship with a null value.
     */
    public function testSaveWithANullValue()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = 1;
        $allocation->vehicle_id = null;
        $allocation->save();

        $allocation->trackingTasks()
            ->save(new TrackingTask());

        $this->assertNotNull($allocation->trackingTasks);
        $this->assertTrue($allocation->trackingTasks->isNotEmpty());
        $this->assertInstanceOf(Allocation::class, $allocation->trackingTasks->first()->allocation);
    }

    /**
     * Test a relationship with only null values is not supported.
     */
    public function testARelationshipWithOnlyNullValuesIsNotSupported()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = null;
        $allocation->vehicle_id = null;
        $allocation->save();

        $allocation->trackingTasks()
            ->save(new TrackingTask());

        $this->assertNotNull($allocation->trackingTasks);
        $this->assertTrue($allocation->trackingTasks->isEmpty());
        $this->assertNull(TrackingTask::first()->allocation);
    }

    /**
     * Test a relationship with a foreign key is empty on a new instance.
     */
    public function testARelationshipWithAForeignKeyIsEmptyOnANewInstance()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->user_id = null;
        $allocation->save();

        $user = new User();

        $this->assertNotNull($user->allocations);
        $this->assertTrue($user->allocations->isEmpty());
    }

    /**
     * Test the create method on a relationship.
     */
    public function testCreate()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = 1;
        $allocation->vehicle_id = 1;
        $allocation->save();

        $allocation->trackingTasks()
            ->create([]);

        $this->assertNotNull($allocation->trackingTasks);
        $this->assertInstanceOf(Allocation::class, $allocation->trackingTasks->first()->allocation);
    }

    /**
     * Test the make method on a relationship.
     */
    public function testMake()
    {
        Model::unguard();

        $allocation = new Allocation();
        $allocation->booking_id = 1;
        $allocation->vehicle_id = 1;
        $allocation->save();

        $trackingTask = $allocation->trackingTasks()
            ->make([]);

        $this->assertNotNull($trackingTask);
        $this->assertInstanceOf(Allocation::class, $trackingTask->allocation);
    }

    public function testHas()
    {
        if (getLaravelVersion() <= 5.6 && getPHPVersion() >= 7.3) {
            $this->markTestIncomplete('This test is broken on laravel 5.6 with PHP 7.3 and earlier!');
        }

        $allocations = Allocation::has('trackingTasks')
            ->get()
            ->toArray();

        $this->assertIsArray($allocations);
    }

    /**
     * A basic test example.
     */
    public function testWhereHas()
    {
        if (getLaravelVersion() <= 5.6 && getPHPVersion() >= 7.3) {
            $this->markTestIncomplete('This test is broken on laravel 5.6 with PHP 7.3 and earlier!');
        }

        $allocations = Allocation::wherehas('trackingTasks')
            ->get()
            ->toArray();

        $this->assertIsArray($allocations);

        $allocations = Allocation::query()
            ->has('trackingTasks')
            ->get()
            ->toArray();

        $this->assertIsArray($allocations);
    }

    public function testWhereHasCallback()
    {
        if (getLaravelVersion() <= 5.6 && getPHPVersion() >= 7.3) {
            $this->markTestIncomplete('This test is broken on laravel 5.6 with PHP 7.3 and earlier!');
        }

        $allocations = Allocation::wherehas('trackingTasks', function ($query) {
            $query->where('vehicle_id', 1);
        })
            ->get()
            ->toArray();

        $this->assertIsArray($allocations);
    }

    public function testMixedTypeCompositeKey()
    {
        Model::unguard();

        $pickupPoint = new PickupPoint();
        $pickupPoint->contract_number = 'AAA';
        $pickupPoint->pickup_index = 1;
        $pickupPoint->save();

        $pickupPoint->pickupTimes()
            ->create([
                'days' => 'mon tue',
                'pickup_time' => '08:00:00',
            ]);

        $this->assertNotNull($pickupPoint->pickupTimes);
    }

    public function testHasForSelfRelation()
    {
        if (getLaravelVersion() <= 5.6 && getPHPVersion() >= 7.3) {
            $this->markTestIncomplete('This test is broken on laravel 5.6 with PHP 7.3 and earlier!');
        }

        $trackingTask = TrackingTask::has('subTasks')
            ->get()
            ->toArray();

        $this->assertIsArray($trackingTask);
    }

    public function testHasWithBelongsToRelation()
    {
        if (getLaravelVersion() <= 5.6 && getPHPVersion() >= 7.3) {
            $this->markTestIncomplete('This test is broken on laravel 5.6 with PHP 7.3 and earlier!');
        }

        $pickup_times = PickupTime::has('pickupPoint')
            ->get()
            ->toArray();

        $this->assertIsArray($pickup_times);
    }

    public function testAssociateOnbelongsTo()
    {
        Model::unguard();

        $user = new User();
        $user->booking_id = 1;
        $user->save();

        $allocation = new Allocation();
        $allocation->user()->associate($user);

        $this->assertNotNull($allocation->user);
    }
}
