<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Connector;
use Exonet\Powerdns\Transformers\RRSetTransformer;
use Exonet\Powerdns\Zone;
use Mockery;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    private const API_RESPONSE = [
        'rrsets' => [
            [
                'name' => 'record01.test.nl.',
                'type' => 'A',
                'records' => [['content' => '127.0.0.1', 'disabled' => false]],
            ],
            [
                'name' => 'record02.test.nl.',
                'type' => 'MX',
                'records' => [
                    ['content' => '10 mail01.test.nl.', 'disabled' => false],
                    ['content' => '10 mail02.test.nl.', 'disabled' => false],
                ],
            ],
        ],
    ];

    public function testCreateSingleResourceRecord() : void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('patch')->withArgs(['zones/test.nl.', Mockery::on(function (RRSetTransformer $transformer) {
            $data = $transformer->transform();

            $this->assertSame('test.test.nl.', $data->rrsets[0]->name);
            $this->assertSame('A', $data->rrsets[0]->type);
            $this->assertSame(10, $data->rrsets[0]->ttl);
            $this->assertSame('127.0.0.1', $data->rrsets[0]->records[0]->content);

            return true;
        })]);

        $zone = new Zone($connector, 'test.nl');
        $zone->create('test', 'A', '127.0.0.1', 10);
    }

    public function testCreateMultipleResourceRecords() : void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('patch')->withArgs(['zones/test.nl.', Mockery::on(function (RRSetTransformer $transformer) {
            $data = $transformer->transform();

            $this->assertSame('test.test.nl.', $data->rrsets[0]->name);
            $this->assertSame('A', $data->rrsets[0]->type);
            $this->assertSame(10, $data->rrsets[0]->ttl);
            $this->assertSame('127.0.0.1', $data->rrsets[0]->records[0]->content);

            $this->assertSame('test.nl.', $data->rrsets[1]->name);
            $this->assertSame('A', $data->rrsets[1]->type);
            $this->assertSame(20, $data->rrsets[1]->ttl);
            $this->assertSame('127.0.0.1', $data->rrsets[1]->records[0]->content);

            $this->assertSame('test.nl.', $data->rrsets[2]->name);
            $this->assertSame('MX', $data->rrsets[2]->type);
            $this->assertSame(30, $data->rrsets[2]->ttl);
            $this->assertSame('10 mail01.test.nl.', $data->rrsets[2]->records[0]->content);
            $this->assertSame('20 mail02.test.nl.', $data->rrsets[2]->records[1]->content);

            $this->assertSame('test02.test.nl.', $data->rrsets[3]->name);
            $this->assertSame('A', $data->rrsets[3]->type);
            $this->assertSame(40, $data->rrsets[3]->ttl);
            $this->assertSame('127.0.0.1', $data->rrsets[3]->records[0]->content);

            $this->assertSame('test03.test.nl.', $data->rrsets[4]->name);
            $this->assertSame('TXT', $data->rrsets[4]->type);
            $this->assertSame(40, $data->rrsets[4]->ttl);
            $this->assertSame('"v=DMARC1; p=none; rua=mailto:info@test.nl; ruf=mailto:info@test.nl"', $data->rrsets[4]->records[0]->content);

            return true;
        })]);

        $zone = new Zone($connector, 'test.nl');
        $zone->create([
            ['name' => 'test', 'type' => 'A', 'content' => '127.0.0.1', 'ttl' => 10],
            ['name' => '@', 'type' => 'A', 'content' => '127.0.0.1', 'ttl' => 20],
            ['name' => '@', 'type' => 'MX', 'content' => ['10 mail01.test.nl.', '20 mail02.test.nl.'], 'ttl' => 30],
            ['name' => 'test02.test.nl.', 'type' => 'A', 'content' => '127.0.0.1', 'ttl' => 40],
            ['name' => 'test03.test.nl.', 'type' => 'TXT', 'content' => '"v=DMARC1; p=none; rua=mailto:info@test.nl; ruf=mailto:info@test.nl"', 'ttl' => 40],
        ]);
    }

    public function testGetResourceRecords() : void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['zones/test.nl.'])->once()->andReturn(self::API_RESPONSE);

        $zone = new Zone($connector, 'test.nl');

        $this->assertSame(2, $zone->get()->count());
        $this->assertSame(1, $zone->get('MX')->count());
    }

    public function testFindResourceRecords() : void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['zones/test.nl.'])->once()->andReturn(self::API_RESPONSE);

        $zone = new Zone($connector, 'test.nl');

        $this->assertSame(1, $zone->find('record01.test.nl.')->count());
        $this->assertSame(0, $zone->find('record01.test.nl.', 'MX')->count());
    }

    public function testGetCanonicalName(): void
    {
        $connector = Mockery::mock(Connector::class);
        $zone = new Zone($connector, 'test.nl');
        $this->assertSame('test.nl'.'.', $zone->getCanonicalName());
    }
}
