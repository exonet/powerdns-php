<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\Exceptions\ReadOnlyException;
use Exonet\Powerdns\MetaType;

/**
 * @internal
 */
class MetaTest extends FunctionalTestCase
{
    public function testCustomMeta(): void
    {
        $canonicalDomain = 'meta-test.'.time().uniqid(true, true).'.test';
        $this->powerdns->createZone($canonicalDomain, ['ns1.power-dns.test.', 'n2.power-dns.test.']);
        $zone = $this->powerdns->zone($canonicalDomain);
        $zone->meta()->create('X-Example-String', 'test');
        $zone->meta()->create('X-Example-Array', ['test-value-1', 'test-value-2']);

        self::assertSame(5, $zone->meta()->get()->count());

        $firstExample = $zone->meta()->get('X-Example-String');
        self::assertSame(1, $firstExample->count());
        self::assertSame('X-Example-String', $firstExample[0]->getKind());
        self::assertSame([0 => 'test'], $firstExample[0]->getData());

        $secondExample = $zone->meta()->get('X-Example-Array');
        self::assertSame(1, $secondExample->count());
        self::assertSame('X-Example-Array', $secondExample[0]->getKind());
        self::assertSame([0 => 'test-value-1', 1 => 'test-value-2'], $secondExample[0]->getData());

        // Perform an update.
        $firstExample[0]->setData('test-2')->save();
        self::assertSame([0 => 'test-2'], $zone->meta()->get('X-Example-String')[0]->getData());
    }

    public function testReadOnly(): void
    {
        $canonicalDomain = 'meta-test.'.time().uniqid(true, true).'.test';
        $this->powerdns->createZone($canonicalDomain, ['ns1.power-dns.test.', 'n2.power-dns.test.']);
        $zone = $this->powerdns->zone($canonicalDomain);
        $zone->enableDnssec();
        $zone->setNsec3param('1 0 1 ab');

        $this->expectException(ReadOnlyException::class);
        $this->expectExceptionMessage('The meta kind [NSEC3PARAM] is read-only.');
        $zone->meta()->get(MetaType::NSEC3PARAM)[0]->delete();

    }
}
