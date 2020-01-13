<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\RecordType;
use PHPUnit\Framework\TestCase;

class ValidateSOAIncrementTest extends TestCase
{
    public function testCreateSoaIncrementZone() : void
    {
        $powerdns = new Powerdns('127.0.0.1', 'apiKey');
        $zone = $powerdns->zone('soa-increment.test');
        $zone->create('test', RecordType::A, '127.0.0.1', 3600);
    }
}
