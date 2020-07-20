<?php

namespace Exonet\Powerdns\tests\functional;

use Exonet\Powerdns\Powerdns;
use PHPUnit\Framework\TestCase;

/**
 * Test scenario for https://github.com/exonet/powerdns-php/issues/38.
 */
class GitHub38Test extends TestCase
{
    public function testGetEmptyCommentsArray(): void
    {
        $powerdns = new Powerdns('127.0.0.1', 'apiKey');

        // Create a unique zone/domain name.
        $canonicalName = 'github-38.'.time().'.test.';
        $zone = $powerdns->createZone($canonicalName, ['ns1.powerdns-php.', 'ns2.powerdns-php.']);

        $this->assertSame([], $zone->get()[0]->getComments());
    }
}
