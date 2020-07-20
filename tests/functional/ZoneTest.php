<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\Powerdns;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * https://github.com/exonet/powerdns-php/issues/40
     */
    public function testGetZoneObject(): void
    {
        $powerdns = new Powerdns('127.0.0.1', 'apiKey');

        // Create a unique zone/domain name.
        $canonicalName = 'get-zone.'.time().'.test.';
        $zone = $powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        $this->assertSame($zone, $zone->get()[0]->getZone());
    }
}
