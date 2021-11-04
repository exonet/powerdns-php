<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\RecordType;

/**
 * @internal
 */
class ValidateSOAIncrementTest extends FunctionalTestCase
{
    public function testCreateSoaIncrementZone(): void
    {
        $canonicalName = 'soa-increment.'.time().'.test';

        $zone = $this->powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);
        $result = $zone->create('test', RecordType::A, '127.0.0.1', 3600);

        $this->assertTrue($result);

        $zone = $this->powerdns->zone($canonicalName);
        $soaData = $zone->get(RecordType::SOA);

        $expectedSoa = sprintf('ns1.powerdns-php. hostmaster.powerdns-php. %s02 10800 3600 604800 3600', date('Ymd'));

        $this->assertSame($expectedSoa, $soaData[0]->getRecords()[0]->getContent());
    }
}
