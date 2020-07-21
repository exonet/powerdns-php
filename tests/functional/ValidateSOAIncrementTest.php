<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\functional\FunctionalTestCase;
use Exonet\Powerdns\RecordType;

class ValidateSOAIncrementTest extends FunctionalTestCase
{
    private $canonicalName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->canonicalName = 'soa-increment.'.time().'.test';
    }

    public function testCreateSoaIncrementZone(): void
    {
        $zone = $this->powerdns->createZone($this->canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);
        $result = $zone->create('test', RecordType::A, '127.0.0.1', 3600);

        $this->assertTrue($result);
    }

    /**
     * @depends testCreateSoaIncrementZone
     */
    public function testSoaIncrement(): void
    {
        // Use sleep to give PowerDNS time to update the SOA record/zone.
        sleep(1);
        $zone = $this->powerdns->zone($this->canonicalName);
        $soaData = $zone->get(RecordType::SOA);

        $expectedSoa = sprintf('ns1.powerdns-php. hostmaster.powerdns-php. %s02 10800 3600 604800 3600', date('Ymd'));

        $this->assertSame($expectedSoa, $soaData[0]->getRecords()[0]->getContent());
    }
}
