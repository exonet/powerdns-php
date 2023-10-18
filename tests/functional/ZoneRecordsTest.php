<?php

namespace Exonet\Powerdns\tests\functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Resources\ResourceRecord;
use Exonet\Powerdns\Resources\ResourceSet;
use Exonet\Powerdns\Resources\Zone as ZoneResource;

/**
 * @internal
 */
class ZoneRecordsTest extends FunctionalTestCase
{
    use ArraySubsetAsserts;

    private $canonicalName;

    private $dnsRecords = [
        ['name' => 'www', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60, 'comments' => []],
        ['name' => 'www', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60, 'comments' => []],
        ['name' => 'bla', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60, 'comments' => []],
        ['name' => '@', 'type' => RecordType::unknownTypePrefix. 65534, 'content' => '\# 4 aabbccdd', 'ttl' => 60, 'comments' => []],
        ['name' => '@', 'type' => RecordType::AAAA, 'content' => '2a00:1e28:3:1629::1', 'ttl' => 60, 'comments' => []],
        [
            'name' => '@',
            'type' => RecordType::SOA,
            'content' => 'ns1.test. hostmaster.test. 0 10800 3605 604800 3600',
            'ttl' => 60,
            'comments' => [],
        ],
        ['name' => '@', 'type' => RecordType::NS, 'content' => 'ns1.powerdns-php.', 'ttl' => 60, 'comments' => []],
        ['name' => '@', 'type' => RecordType::NS, 'content' => 'ns2.powerdns-php.', 'ttl' => 60, 'comments' => []],
        ['name' => '@', 'type' => RecordType::A, 'content' => '127.0.0.1', 'ttl' => 60, 'comments' => []],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->canonicalName = 'zone-with-records.'.time().'.test.';
    }

    public function testCreateZoneFromResource(): void
    {
        // Create a new zone resource.
        $newZone = new ZoneResource();
        $newZone->setName($this->canonicalName);

        $newZone->setResourceRecords($this->dnsRecords);

        // Create a new zone with the defined records and name servers.
        $zone = $this->powerdns->createZoneFromResource($newZone);

        $this->validateResourceSet($zone->get());
    }

    /**
     * In the defined DNS records the SOA record differs from the default. Validate that it is working as expected.
     *
     * @depends testCreateZoneFromResource
     */
    public function testCustomSoa(): void
    {
        $soaRecord = $this->powerdns->zone($this->canonicalName)->get(RecordType::SOA)[0]->getRecords()[0]->getContent();
        self::assertSame('ns1.test. hostmaster.test. '.date('Ymd').'01 10800 3605 604800 3600', $soaRecord);

        $createResult = $this->powerdns->zone($this->canonicalName)->create('new', RecordType::TXT, '"soa update test"');
        self::assertTrue($createResult);

        $soaRecord = $this->powerdns->zone($this->canonicalName)->get(RecordType::SOA)[0]->getRecords()[0]->getContent();
        self::assertSame('ns1.test. hostmaster.test. '.date('Ymd').'02 10800 3605 604800 3600', $soaRecord);
    }

    private function validateResourceSet(ResourceSet $resourceSet): void
    {
        $createdRecords = [];
        $resourceSet->map(
            function (ResourceRecord $item) use (&$createdRecords) {
                foreach ($item->getRecords() as $recordContent) {
                    $content = $recordContent->getContent();

                    if ($item->getType() === RecordType::SOA) {
                        $content = str_replace(date('Ymd').'01', '0', $content);
                    }

                    $name = rtrim(str_replace($this->canonicalName, '', $item->getName()), '.');
                    $createdRecords[] = [
                        'name' => $name === '' ? '@' : $name,
                        'type' => $item->getType(),
                        'content' => $content,
                        'ttl' => $item->getTtl(),
                        'comments' => [],
                    ];
                }
            }
        );

        self::assertArraySubset($this->dnsRecords, $createdRecords);
    }
}
