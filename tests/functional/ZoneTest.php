<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\functional\FunctionalTestCase;

class ZoneTest extends FunctionalTestCase
{
    /**
     * https://github.com/exonet/powerdns-php/issues/40.
     */
    public function testGetZoneObject(): void
    {
        // Create a unique zone/domain name.
        $canonicalName = 'get-zone.'.time().'.test.';
        $zone = $this->powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        $this->assertSame($zone, $zone->get()[0]->getZone());
    }

    /**
     * https://github.com/exonet/powerdns-php/issues/35.
     */
    public function testExportZone(): void
    {
        // Create a unique zone/domain name.
        $canonicalName = 'export-zone.'.time().'.test.';
        $zone = $this->powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        if (strpos($this->powerdns->serverVersion(), '4.1') !== 0) {
            $this->assertSame(
                sprintf(
                    '%1$s	3600	IN	NS	ns1.powerdns-php.
%1$s	3600	IN	NS	ns2.powerdns-php.
%1$s	3600	IN	SOA	ns1.powerdns-php. hostmaster.powerdns-php. %2$d01 10800 3600 604800 3600
',
                    $canonicalName,
                    date('Ymd')
                ),
                $zone->export()
            );
        } else {
            $this->assertSame(
                sprintf(
                    '%1$s	3600	NS	ns1.powerdns-php.
%1$s	3600	NS	ns2.powerdns-php.
%1$s	3600	SOA	ns1.powerdns-php. hostmaster.powerdns-php. %2$d01 10800 3600 604800 3600
',
                    $canonicalName,
                    date('Ymd')
                ),
                $zone->export()
            );
        }
    }
}
