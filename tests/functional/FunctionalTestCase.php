<?php

namespace Exonet\Powerdns\functional;

use Exonet\Powerdns\Powerdns;
use PHPUnit\Framework\TestCase;

abstract class FunctionalTestCase extends TestCase
{
    /**
     * @var Powerdns The PowerDNS instance.
     */
    protected $powerdns;

    protected function setUp(): void
    {
        parent::setUp();

        $host = getenv('PDNS_HOST') ?: '127.0.0.1';

        $this->powerdns = new Powerdns($host, 'apiKey', 8081);
    }

}
