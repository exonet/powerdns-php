<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Resources\ResourceRecord;
use Exonet\Powerdns\Resources\ResourceSet;
use Exonet\Powerdns\Zone;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ResourceSetTest extends TestCase
{
    public function testGeneralFunctionality(): void
    {
        $zone = Mockery::mock(Zone::class);

        $resourceRecord = new ResourceRecord();
        $resourceRecord->setTtl(3600);

        $resourceSet = new ResourceSet($zone);

        $this->assertTrue($resourceSet->isEmpty());
        $this->assertFalse($resourceSet->isNotEmpty());

        $resourceSet->addResource($resourceRecord);

        $this->assertFalse($resourceSet->isEmpty());
        $this->assertTrue($resourceSet->isNotEmpty());
        $this->assertSame(1, $resourceSet->count());
        $this->assertSame($resourceRecord, $resourceSet[0]);
        $this->assertSame(3600, $resourceSet[0]->getTtl());

        $resourceSet->map(function (ResourceRecord $resourceRecord) {
            return $resourceRecord->setTtl(1234);
        });

        $this->assertSame(1234, $resourceSet[0]->getTtl());
    }

    public function testSave(): void
    {
        $resourceRecord = new ResourceRecord();

        $zone = Mockery::mock(Zone::class);
        $zone->shouldReceive('patch')->withArgs([[$resourceRecord]])->once()->andReturnTrue();

        $resourceSet = new ResourceSet($zone, [$resourceRecord]);
        $this->assertTrue($resourceSet->save());
    }

    public function testDelete(): void
    {
        $resourceRecord = Mockery::mock(ResourceRecord::class);
        $resourceRecord->shouldReceive('setChangeType')->withArgs(['DELETE'])->once()->andReturnSelf();

        $zone = Mockery::mock(Zone::class);
        $zone->shouldReceive('patch')->withArgs([[$resourceRecord]])->once()->andReturnTrue();

        $resourceSet = new ResourceSet($zone, [$resourceRecord]);
        $this->assertTrue($resourceSet->delete());
    }
}
