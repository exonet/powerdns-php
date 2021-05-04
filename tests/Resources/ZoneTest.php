<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Exceptions\InvalidKindType;
use Exonet\Powerdns\Exceptions\InvalidNsec3Param;
use Exonet\Powerdns\Exceptions\InvalidSoaEditType;
use Exonet\Powerdns\Resources\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    public function testSetApiResponse(): void
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

    public function testSetApiResponseWithoutOptionalData(): void
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

    public function testSetNameservers(): void
    {
        $zone = new Zone();
        $zone->setNameservers(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $zone->getNameservers());
    }

    public function testSetKind(): void
    {
        $zone = new Zone();
        $zone->setKind('master');
        $this->assertSame('Master', $zone->getKind());

        $this->expectException(InvalidKindType::class);
        $this->expectExceptionMessage('Kind must be either Native, Master, Slave. (FooBar given)');
        $zone->setKind('FooBar');
    }

    public function testSetSoaEditApi(): void
    {
        $zone = new Zone();
        $zone->setSoaEditApi('increase');
        $this->assertSame('INCREASE', $zone->getSoaEditApi());

        $this->expectException(InvalidSoaEditType::class);
        $this->expectExceptionMessage('Kind must be either DEFAULT, INCREASE, EPOCH, SOA-EDIT, SOA-EDIT-INCREASE. (FOOBAR given)');
        $zone->setSoaEditApi('FooBar');
    }

    public function testSetSoaEdit(): void
    {
        $zone = new Zone();
        $zone->setSoaEdit('inception-increment');
        $this->assertSame('INCEPTION-INCREMENT', $zone->getSoaEdit());

        $this->expectException(InvalidSoaEditType::class);
        $this->expectExceptionMessage('Kind must be either INCREMENT-WEEKS, INCEPTION-EPOCH, INCEPTION-INCREMENT, EPOCH, NONE. (FOOBAR given)');
        $zone->setSoaEdit('FooBar');
    }

    public function testSetNsec3param(): void
    {
        $zone = new Zone();
        $zone->setNsec3param('1 0 100 f00Bar');
        $this->assertEquals('1 0 100 f00Bar', $zone->getNsec3param());
    }

    public function testSetEmptyNsec3param(): void
    {
        $zone = new Zone();
        $zone->setNsec3param();
        $this->assertNull($zone->getNsec3param());
    }

    public function testSetNsec3paramInvalidAlgorithm(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('2 0 100 f00bar');
    }

    public function testSetNsec3paramInvalidFlags(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 2 100 f00bar');
    }

    public function testSetNsec3paramInvalidIteration(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 0 25000 f00bar');
    }

    public function testSetNsec3paramInvalidSalt(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 0 100 '.str_repeat('a', 256));
    }

    public function testSetNsec3paramTooFewArguments(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('1 0');
    }

    public function tesSetNsec3paramInvalidArgument(): void
    {
        $this->expectException(InvalidNsec3Param::class);
        $zone = new Zone();
        $zone->setNsec3param('unit-test');
    }
}
