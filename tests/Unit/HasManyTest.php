<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/compoships.
 *
 * @link     https://github.com/friendsofhyperf/compoships
 * @document https://github.com/friendsofhyperf/compoships/blob/master/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Awobaz\Compoships\Tests\Unit;

use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;
use Awobaz\Compoships\Tests\Models\Allocation;
use Awobaz\Compoships\Tests\Models\OriginalPackage;
use Awobaz\Compoships\Tests\Models\TrackingTask;
use Awobaz\Compoships\Tests\TestCase\TestCase;
use Carbon\Carbon;
use Hyperf\Database\Capsule\Manager as Capsule;

/**
 * @covers \Awobaz\Compoships\Compoships::getAttribute
 * @covers \Awobaz\Compoships\Compoships::newBaseQueryBuilder
 * @covers \Awobaz\Compoships\Compoships::qualifyColumn
 * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::getQualifiedKeyName
 * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::hasMany
 * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::newHasMany
 * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::sanitizeKey
 * Generic:
 * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::validateRelatedModel
 * @covers \Awobaz\Compoships\Database\Eloquent\Relations\HasMany
 * @covers \Awobaz\Compoships\Database\Eloquent\Relations\HasOneOrMany
 *
 * @internal
 */
class HasManyTest extends TestCase
{
    /**
     * @test
     */
    public function brokenComposhipsHasOneOrManyWhereInMethodMissingRelationColumn()
    {
        $this->markAsRisky();
        $this->markTestIncomplete('This test is broken, because relation columns are required on selections!');
        $allocation = $this->createAllocation();
        $allocation->trackingTasks()
            ->saveMany([
                new TrackingTask(),
                new TrackingTask(),
                new TrackingTask(),
            ]);

        $trackingTasks = Allocation::where('id', $allocation->id)
            ->with(
                [
                    'trackingTasks' => function (HasMany $query) {
                        // missing 'vehicle column'
                        $query->select('booking_id');
                    },
                ]
            )
            ->first()->trackingTasks;
        $this->assertCount(1, $trackingTasks); // TODO: must be error or 3 items?
    }

    /**
     * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::belongsTo
     * @covers \Awobaz\Compoships\Database\Eloquent\Concerns\HasRelationships::newBelongsTo
     * @covers \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo::addConstraints
     * @covers \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo::getResults
     */
    public function testComposhipsHasOneOrManySaveMany()
    {
        $expectedData = [
            [
                'id' => (string) 1,
                'booking_id' => (string) 1,
                'vehicle_id' => (string) 1,
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'deleted_at' => null,
            ],
            [
                'id' => (string) 2,
                'booking_id' => (string) 1,
                'vehicle_id' => (string) 1,
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'deleted_at' => null,
            ],
            [
                'id' => (string) 3,
                'booking_id' => (string) 1,
                'vehicle_id' => (string) 1,
                'updated_at' => Carbon::now()
                    ->toDateTimeString(),
                'created_at' => Carbon::now()
                    ->toDateTimeString(),
                'deleted_at' => null,
            ],
        ];

        $allocation = $this->createAllocation();
        $allocation->trackingTasks()->saveMany([
            new TrackingTask(),
            new TrackingTask(),
            new TrackingTask(),
        ]);
        $this->assertNotNull($allocation->trackingTasks);
        $this->assertEquals(count($expectedData), $allocation->trackingTasks->count());
        $this->assertEquals($expectedData, $allocation->trackingTasks->toArray());
        $this->assertEquals($expectedData, array_map(function ($item) {
            return (array) $item;
        }, Capsule::table('tracking_tasks')->get()->all()));

        if (getLaravelVersion() >= 8) {
            $this->assertEquals(1, $allocation->smallerTrackingTask->id);
            $this->assertEquals(1, $allocation->oldestTrackingTask->id);
            $this->assertEquals(3, $allocation->latestTrackingTask->id);
        }
    }

    public function testComposhipsHasOneOrManyCreateEmpty()
    {
        $allocation = $this->createAllocation();
        $trackingTask = $allocation->trackingTasks()->create();
        $this->assertEquals([
            'id' => 1,
            'booking_id' => 1,
            'vehicle_id' => 1,
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'created_at' => Carbon::now()
                ->toDateTimeString(),
        ], $trackingTask->toArray());

        $this->assertEquals(1, Capsule::table('tracking_tasks')
            ->count());
        $this->AssertEquals([
            'id' => (string) 1,
            'booking_id' => (string) 1,
            'vehicle_id' => (string) 1,
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'deleted_at' => null,
        ], (array) Capsule::table('tracking_tasks')
            ->select()
            ->first());

        $trackingTask->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'booking_id' => (string) 1,
            'vehicle_id' => (string) 1,
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'deleted_at' => null,
        ], $trackingTask->toArray());
    }

    public function testComposhipsHasOneOrManyCreateFailSetUnguarded()
    {
        $this->expectException(\Hyperf\Database\Model\MassAssignmentException::class);
        $this->expectExceptionMessage('Add [created_at] to fillable property to allow mass assignment');
        $allocation = $this->createAllocation();
        $allocation->trackingTasks()->create(['created_at' => Carbon::now()]);
    }

    public function testComposhipsHasOneOrManyCreateChangeRelationColumns()
    {
        $allocation = $this->createAllocation();
        $allocation::unguard();
        $package = $allocation->trackingTasks()->create(['booking_id' => 123]);
        $package->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'booking_id' => (string) 1,  // correct, as it was not changed
            'vehicle_id' => (string) 1,
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'deleted_at' => null,
        ], $package->toArray());
    }

    public function testComposhipsHasOneOrManyCreateNormal()
    {
        $allocation = $this->createAllocation();
        $allocation::unguard();
        $trackingTask = $allocation->trackingTasks()->create(['created_at' => Carbon::now()->addDay()]);
        $trackingTask->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'booking_id' => (string) 1,
            'vehicle_id' => (string) 1,
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'created_at' => Carbon::now()
                ->addDay()
                ->toDateTimeString(),
            'deleted_at' => null,
        ], $trackingTask->toArray());
    }

    /**
     * @covers \Awobaz\Compoships\Database\Query\Builder::whereIn
     */
    public function testComposhipsEagerLoading()
    {
        $allocationId1 = Capsule::table('allocations')->insertGetId([
            'booking_id' => 1,
            'vehicle_id' => 1,
        ]);
        $allocationId2 = Capsule::table('allocations')->insertGetId([
            'booking_id' => 2,
            'vehicle_id' => 2,
        ]);
        Capsule::table('tracking_tasks')->insertGetId([
            'booking_id' => 1,
            'vehicle_id' => 1,
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'deleted_at' => null,
        ]);
        Capsule::table('tracking_tasks')->insertGetId([
            'booking_id' => 1,
            'vehicle_id' => 1,
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'deleted_at' => null,
        ]);

        $allocations1 = Allocation::where('id', $allocationId1)->with('trackingTasks', 'smallerTrackingTask', 'latestTrackingTask', 'oldestTrackingTask')->get()->all();
        $allocations2 = Allocation::where('id', $allocationId2)->with('trackingTasks', 'smallerTrackingTask', 'latestTrackingTask', 'oldestTrackingTask')->get()->all();

        $this->assertCount(1, $allocations1);
        $this->assertCount(2, $allocations1[0]->trackingTasks);
        $this->assertEquals(1, $allocations1[0]->trackingTasks[0]->id);
        $this->assertEquals(2, $allocations1[0]->trackingTasks[1]->id);

        $this->assertCount(1, $allocations2);
        $this->assertCount(0, $allocations2[0]->trackingTasks);

        if (getLaravelVersion() >= 8) {
            $this->assertEquals(1, $allocations1[0]->smallerTrackingTask->id);
            $this->assertEquals(1, $allocations1[0]->oldestTrackingTask->id);
            $this->assertEquals(2, $allocations1[0]->latestTrackingTask->id);

            $this->assertEquals(null, $allocations2[0]->smallerTrackingTask);
            $this->assertEquals(null, $allocations2[0]->oldestTrackingTask);
            $this->assertEquals(null, $allocations2[0]->latestTrackingTask);
        }
    }

    /**
     * @covers \Awobaz\Compoships\Database\Query\Builder::whereIn
     */
    public function testIlluminateEagerLoading()
    {
        $allocationId1 = Capsule::table('allocations')->insertGetId([
            'booking_id' => 1,
            'vehicle_id' => 1,
        ]);

        $allocationId2 = Capsule::table('allocations')->insertGetId([
            'booking_id' => 2,
            'vehicle_id' => 2,
        ]);
        Capsule::table('original_packages')->insertGetId([
            'name' => 'name 1',
            'allocation_id' => 1,
        ]);
        Capsule::table('original_packages')->insertGetId([
            'name' => 'name 2',
            'allocation_id' => 1,
        ]);

        $allocations1 = Allocation::where('id', $allocationId1)->with('originalPackages')->get()->all();
        $this->assertCount(1, $allocations1);
        $this->assertCount(2, $allocations1[0]->originalPackages);
        $this->assertEquals(1, $allocations1[0]->originalPackages[0]->id);
        $this->assertEquals(2, $allocations1[0]->originalPackages[1]->id);

        $allocations2 = Allocation::where('id', $allocationId2)->with('originalPackages')->get()->all();
        $this->assertCount(1, $allocations2);
        $this->assertCount(0, $allocations2[0]->originalPackages);

        if (getLaravelVersion() >= 8) {
            $allocations1 = Allocation::where('id', $allocationId1)->with('originalPackagesOneOfMany')->get()->all();
            $this->assertEquals(2, $allocations1[0]->originalPackagesOneOfMany->id);

            $allocations2 = Allocation::where('id', $allocationId2)->with('originalPackagesOneOfMany')->get()->all();
            $this->assertEquals(null, $allocations2[0]->originalPackagesOneOfMany);
        }
    }

    public function testIlluminateHasOneOrManyCreateNormal()
    {
        $allocation = $this->createAllocation();
        $allocation::unguard();
        $package = $allocation->originalPackages()->create(['name' => 'some name']);
        $package->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'allocation_id' => (string) 1,
            'name' => 'some name',
            'pcid' => null,
        ], $package->toArray());
    }

    public function testIlluminateHasOneOrManyCreateChangeRelationColumns()
    {
        $allocation = $this->createAllocation();
        $allocation::unguard();
        $package = $allocation->originalPackages()->create(['allocation_id' => 123]);
        $package->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'allocation_id' => (string) 1, // correct, as it was not changed
            'name' => null,
            'pcid' => null,
        ], $package->toArray());
    }

    public function testIlluminateHasOneOrManyCreateEmpty()
    {
        $allocation = $this->createAllocation();
        $package = $allocation->originalPackages()->create();
        $package->refresh();
        $this->assertEquals([
            'id' => (string) 1,
            'allocation_id' => (string) 1,
            'name' => null,
            'pcid' => null,
        ], $package->toArray());
    }

    public function testIlluminateHasOneOrManyCreateFailSetUnguarded()
    {
        $this->expectException(\Hyperf\Database\Model\MassAssignmentException::class);
        $this->expectExceptionMessage('Add [name] to fillable property to allow mass assignment');
        $allocation = $this->createAllocation();
        $allocation->originalPackages()->create(['name' => 'some name']);
    }

    public function testIlluminateHasOneOrManySaveMany()
    {
        $expectedData = [
            [
                'id' => (string) 1,
                'allocation_id' => (string) 1,
                'name' => 'name 1',
                'pcid' => null,
            ],
            [
                'id' => (string) 2,
                'allocation_id' => (string) 1,
                'name' => 'name 2',
                'pcid' => null,
            ],
            [
                'id' => (string) 3,
                'allocation_id' => (string) 1,
                'name' => 'name 3',
                'pcid' => null,
            ],
        ];

        $allocation = $this->createAllocation(1, 1);
        $allocation::unguard();
        $allocation->originalPackages()->saveMany([
            new OriginalPackage(['name' => 'name 1']),
            new OriginalPackage(['name' => 'name 2']),
            new OriginalPackage(['name' => 'name 3']),
        ]);
        $allocation->refresh();
        $this->assertNotNull($allocation->originalPackages);
        $this->assertEquals(count($expectedData), $allocation->originalPackages->count());
        $this->assertEquals($expectedData, $allocation->originalPackages->toArray());
        $this->assertEquals($expectedData, array_map(function ($item) {
            return (array) $item;
        }, Capsule::table('original_packages')->get()->all()));
    }

    /**
     * @param int $bookingId
     * @param int $vehicleId
     *
     * @return Allocation
     */
    protected function createAllocation($bookingId = 1, $vehicleId = 1)
    {
        $allocation = new Allocation();
        $allocation->booking_id = $bookingId;
        $allocation->vehicle_id = $vehicleId;
        $allocation->save();
        $this->assertEquals(1, Capsule::table('allocations')
            ->count());
        $this->assertEquals([
            'id' => (string) 1,
            'booking_id' => (string) $allocation->booking_id,
            'vehicle_id' => (string) $allocation->vehicle_id,
            'created_at' => Carbon::now()
                ->toDateTimeString(),
            'updated_at' => Carbon::now()
                ->toDateTimeString(),
            'user_id' => null,
        ], (array) Capsule::table('allocations')
            ->first());

        return $allocation;
    }
}
