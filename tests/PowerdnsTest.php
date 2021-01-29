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
    public function testConfigViaConstructor(): void
    {
        $powerDns = new Powerdns('test-host', 'test-key', 1234, 'test-server');
        $config = $powerDns->getConfig();

        $this->assertSame('test-host', $config['host']);
        $this->assertSame(1234, $config['port']);
        $this->assertSame('test-server', $config['server']);
        $this->assertSame('test-key', $config['apiKey']);
    }

    public function testConfigViaMethods(): void
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

    public function testStatistics(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['statistics?includerings=false'])->once()->andReturn($example = [
            [
                'name' => 'corrupt-packets',
                'type' => 'StatisticItem',
                'value' => 0,
            ],
        ]);

        $powerDns = new Powerdns(null, null, null, null, $connector);
        $stats = $powerDns->statistics();

        self::assertEquals($example, $stats);
    }

    public function testStatisticsWithParams(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['statistics?includerings=true&statistic=corrupt-packets'])->once()->andReturn($example = [
            [
                'name' => 'corrupt-packets',
                'type' => 'StatisticItem',
                'value' => 0,
            ],
        ]);

        $powerDns = new Powerdns(null, null, null, null, $connector);
        $stats = $powerDns->statistics('corrupt-packets', true);

        self::assertEquals($example, $stats);
    }

    public function testZone(): void
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

    public function listZonesArgumentDataProvider(): array
    {
        return [['true', true], ['false', false]];
    }

    /**
     * @dataProvider listZonesArgumentDataProvider
     */
    public function testListZones(string $dnssecArgument, bool $listZonesArgument): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['zones?dnssec=' . $dnssecArgument])->once()->andReturn(
            [
                [
                    'account' => '',
                    'dnssec' => false,
                    'id' => 'example.co.uk',
                    'kind' => 'Native',
                    'last_check' => 0,
                    'masters' => [],
                    'name' => 'example.',
                    'notified_serial' => 0,
                    'serial' => 2019100101,
                    'url' => '/api/v1/servers/localhost/zones/example.co.uk',
                ],
                [
                    'account' => '',
                    'dnssec' => false,
                    'id' => 'example.com',
                    'kind' => 'Native',
                    'last_check' => 0,
                    'masters' => [],
                    'name' => 'example.',
                    'notified_serial' => 0,
                    'serial' => 2019100102,
                    'url' => '/api/v1/servers/localhost/zones/example.com',
                ],
            ]
        );

        $powerDns = new Powerdns(null, null, null, null, $connector);

        $response = $powerDns->listZones($listZonesArgument);
        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        foreach ($response as $zone) {
            $this->assertInstanceOf(Zone::class, $zone);
        }
    }

    public function testSearch(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector
            ->shouldReceive('get')
            ->withArgs(['search-data?q=search+str%C3%AFng%26more&max=1337&object_type=zone'])
            ->once()
            ->andReturn(
                [
                    [
                        'content' => 'test content',
                        'disabled' => false,
                        'name' => 'test name',
                        'object_type' => 'zone',
                        'zone_id' => 'zone.test.',
                        'zone' => 'zone.test.',
                        'type' => 'zone type',
                        'ttl' => 1234,
                    ],
                ]
            );

        $powerDns = new Powerdns(null, null, null, null, $connector);

        $searchResults = $powerDns->search('search strïng&more', 1337, 'zone');

        $this->assertSame(1, $searchResults->count());
        $this->assertSame('test content', $searchResults[0]->getContent());
        $this->assertFalse($searchResults[0]->isDisabled());
        $this->assertSame('test name', $searchResults[0]->getName());
        $this->assertSame('zone', $searchResults[0]->getObjectType());
        $this->assertSame('zone.test.', $searchResults[0]->getZoneId());
        $this->assertSame('zone.test.', $searchResults[0]->getZone());
        $this->assertSame('zone type', $searchResults[0]->getType());
        $this->assertSame(1234, $searchResults[0]->getTtl());
    }
}
