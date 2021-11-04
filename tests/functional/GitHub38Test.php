<?php

namespace Exonet\Powerdns\tests\functional;

/**
 * Test scenario for https://github.com/exonet/powerdns-php/issues/38.
 *
 * @internal
 */
class GitHub38Test extends FunctionalTestCase
{
    public function testGetEmptyCommentsArray(): void
    {
        // Create a unique zone/domain name.
        $canonicalName = 'github-38.'.time().'.test.';
        $zone = $this->powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        $this->assertSame([], $zone->get()[0]->getComments());
    }
}
