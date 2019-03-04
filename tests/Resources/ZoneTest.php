<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Exceptions\InvalidKindType;
use Exonet\Powerdns\Exceptions\InvalidNsec3Param;
use Exonet\Powerdns\Exceptions\InvalidSoaEditType;
use Exonet\Powerdns\Resources\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    public function test_SetApiResponse_WithFullData_GetData()
    {
        $zone = new Zone();

        $zone->setApiResponse([
            'name' => 'test name',
            'kind' => 'test kind',
            'serial' => 4321,
            'notified_serial' => 1234,
            'masters' => ['test master 1', 'test master 2'],
            'dnssec' => true,
            'nsec3param' => '1 0 100 fooBar',
            'soa_edit_api' => 'test soa_edit_api',
            'api_rectify' => true,
            'account' => 'test account',
        ]);

        $this->assertSame('test name', $zone->getName());
        $this->assertSame('test kind', $zone->getKind());
        $this->assertSame(4321, $zone->getSerial());
        $this->assertSame(1234, $zone->getNotifiedSerial());
        $this->assertSame(['test master 1', 'test master 2'], $zone->getMasters());
        $this->assertTrue($zone->hasDnssec());
        $this->assertSame('1 0 100 fooBar', $zone->getNsec3param());
        $this->assertSame('test soa_edit_api', $zone->getSoaEditApi());
        $this->assertTrue($zone->hasAutoRectify());
        $this->assertSame('test account', $zone->getAccount());
        $this->assertSame([], $zone->getNameservers());
    }

    public function test_SetApiResponse_WithOptionalData_GetData()
    {
        $zone = new Zone();

        $zone->setApiResponse([
            'name' => 'test name',
            'kind' => 'test kind',
            'serial' => 4321,
            'notified_serial' => 1234,
            'masters' => ['test master 1', 'test master 2'],
            'dnssec' => false,
            'nsec3param' => null,
            'soa_edit_api' => '',
            'api_rectify' => false,
            'account' => '',
        ]);

        $this->assertNull($zone->getNsec3param());
        $this->assertNull($zone->getSoaEditApi());
        $this->assertNull($zone->getAccount());
    }

    public function test_SetNameservers_NameserverSet()
    {
        $zone = new Zone();
        $zone->setNameservers(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $zone->getNameservers());
    }

    public function test_setKind_InvalidKind_ExceptionThrown()
    {
        $this->expectException(InvalidKindType::class);
        $zone = new Zone();
        $zone->setKind('FooBar');
    }

    public function test_setSoaEditApi_InvalidValue_ExceptionThrown()
    {
        $this->expectException(InvalidSoaEditType::class);
        $zone = new Zone();
        $zone->setSoaEditApi('FooBar');
    }

    public function test_setNsec3param_InvalidAlgorithm_ExceptionThrown()
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('2 0 100 f00bar');
    }

    public function test_setNsec3param_InvalidFlags_ExceptionThrown()
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 1 100 f00bar');
    }

    public function test_setNsec3param_InvalidIteration_ExceptionThrown()
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 0 25000 f00bar');
    }

    public function test_setNsec3param_InvalidSalt_ExceptionThrown()
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 0 100 '.str_repeat('a', 256));
    }

    public function test_setNsec3param_ProperParM_GetParam()
    {
        $zone = new Zone();
        $zone->setNsec3param('1 0 100 f00Bar');
        $this->assertEquals('1 0 100 f00Bar', $zone->getNsec3param());
    }
}
