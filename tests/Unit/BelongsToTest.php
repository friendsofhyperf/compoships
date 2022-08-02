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

use Awobaz\Compoships\Tests\Models\Allocation;
use Awobaz\Compoships\Tests\Models\OriginalPackage;
use Awobaz\Compoships\Tests\Models\ProductCode;
use Awobaz\Compoships\Tests\TestCase\TestCase;
use Hyperf\Database\Capsule\Manager as Capsule;
use Hyperf\Database\Model\Model;

/**
 * @internal
 * @coversNothing
 */
class BelongsToTest extends TestCase
{
    /**
     * @covers \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo
     */
    public function testUuidNoInrecemntRelation()
    {
        Model::unguard();

        $pcid = uniqid();
        $productCode = new ProductCode([
            'pcid' => $pcid,
            'code' => 'AAA-BBB-CCC',
        ]);
        $productCode->save();

        $allocation = new Allocation();
        $allocation->save();

        /** @var OriginalPackage $package */
        $package = $allocation->originalPackages()->create([
            'pcid' => $pcid,
        ]);

        $dbPackage = Capsule::table('original_packages')->find($package->id);

        $this->assertEquals(1, Capsule::table('original_packages')->count());
        $this->assertNotNull($dbPackage);
        $this->assertEquals($pcid, $package->pcid);
        $this->assertEquals($pcid, $dbPackage->pcid);
        $this->assertInstanceOf(ProductCode::class, $package->productCode);
        $this->assertEquals($pcid, $package->productCode->pcid);

        $this->assertEquals($package->productCode, $package->productCode2);
    }
}
