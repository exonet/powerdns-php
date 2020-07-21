<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\functional\FunctionalTestCase;

class ZoneTest extends FunctionalTestCase
{
    /**
     * https://github.com/exonet/powerdns-php/issues/40
     */
    public function testGetZoneObject(): void
    {
        // Create a unique zone/domain name.
        $canonicalName = 'get-zone.'.time().'.test.';
        $zone = $this->powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        $this->assertSame($zone, $zone->get()[0]->getZone());
    }
}
