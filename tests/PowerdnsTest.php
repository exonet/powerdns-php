<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Connector;
use Exonet\Powerdns\Powerdns;
use Exonet\Powerdns\Transformers\CreateZoneTransformer;
use Exonet\Powerdns\Zone;
use Mockery;
use PHPUnit\Framework\TestCase;

class PowerdnsTest extends TestCase
{
    public function testConfigViaConstructor() : void
    {
        $powerDns = new Powerdns('test-host', 'test-key', 1234, 'test-server');
        $config = $powerDns->getConfig();

        $this->assertSame('test-host', $config['host']);
        $this->assertSame(1234, $config['port']);
        $this->assertSame('test-server', $config['server']);
        $this->assertSame('test-key', $config['apiKey']);
    }

    public function testConfigViaMethods() : void
    {
        $powerDns = new Powerdns();
        $powerDns->connect('test-host', 1234, 'test-server');
        $powerDns->useKey('test-key');
        $config = $powerDns->getConfig();

        $this->assertSame('test-host', $config['host']);
        $this->assertSame(1234, $config['port']);
        $this->assertSame('test-server', $config['server']);
        $this->assertSame('test-key', $config['apiKey']);
    }

    public function testZone() : void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('post')->withArgs(['zones', Mockery::on(function (CreateZoneTransformer $transformer) {
            $data = $transformer->transform();

            $this->assertSame('test.nl.', $data->name);
            $this->assertSame(['ns1.test.nl.', 'ns2.test.nl.'], $data->nameservers);

            return true;
        })])->once();
        $connector->shouldReceive('delete')->withArgs(['zones/test.nl.'])->once();

        $powerDns = new Powerdns(null, null, null, null, $connector);

        $zone = $powerDns->createZone('test.nl', ['ns1.test.nl.', 'ns2.test.nl.']);

        $this->assertInstanceOf(Zone::class, $zone);
        $this->assertTrue($powerDns->deleteZone('test.nl.'));
    }
}
