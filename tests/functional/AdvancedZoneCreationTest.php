<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\functional\FunctionalTestCase;
use Exonet\Powerdns\Resources\Zone as ZoneResource;

class AdvancedZoneCreationTest extends FunctionalTestCase
{
    public function testCreateSoaIncrementZone(): void
    {
        // Create a unique zone/domain name.
        $canonicalDomain = 'advanced-zone.'.time().'.test.';

        // Create a new zone resource.
        $newZone = new ZoneResource();
        $newZone->setName($canonicalDomain);
        $newZone->setKind('native');
        $newZone->setDnssec(true);
        $newZone->setSoaEdit('epoch');
        $newZone->setSoaEditApi('epoch');
        $newZone->setNameservers(['ns1.test.']);
        $newZone->setApiRectify(false);
        $newZone->setNsec3param('1 0 100 1234567890');
        $newZone->setAccount('account-name');

        // Create a new zone with the defined records and name servers.
        $this->powerdns->createZoneFromResource($newZone);

        // Get the zone and check the results.
        $zone = $this->powerdns->zone($canonicalDomain);
        $zoneResource = $zone->resource();
        $this->assertSame($canonicalDomain, $zone->getCanonicalName());
        $this->assertSame($canonicalDomain, $zoneResource->getName());
        $this->assertSame('Native', $zoneResource->getKind());
        $this->assertTrue($zoneResource->hasDnssec(), 'DNSSEC not correctly set.');
        $this->assertSame('EPOCH', $zoneResource->getSoaEdit());
        $this->assertSame('EPOCH', $zoneResource->getSoaEditApi());
        $this->assertSame(['ns1.test.'], $zoneResource->getNameservers());
        $this->assertFalse($zoneResource->hasAutoRectify(), 'API Rectify not correctly set.');
        $this->assertSame('1 0 100 1234567890', $zoneResource->getNsec3param());
        $this->assertSame('account-name', $zoneResource->getAccount());
    }
}
