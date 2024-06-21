<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Connector;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Resources\Comment;
use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;
use Exonet\Powerdns\Resources\Zone as ZoneResource;
use Exonet\Powerdns\Transformers\RRSetTransformer;
use Exonet\Powerdns\Zone;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ZoneTest extends TestCase
{
    public const API_RESPONSE = [
        'name' => 'test.nl.',
        'kind' => 'Native',
        'serial' => 123,
        'notified_serial' => 123,
        'masters' => [],
        'dnssec' => false,
        'api_rectify' => true,
        'rrsets' => [
            [
                'name' => 'record01.test.nl.',
                'type' => 'A',
                'ttl' => 3600,
                'comments' => [],
                'records' => [['content' => '127.0.0.1', 'disabled' => false]],
            ],
            [
                'name' => 'record02.test.nl.',
                'type' => 'MX',
                'ttl' => 3600,
                'comments' => [],
                'records' => [
                    ['content' => '10 mail01.test.nl.', 'disabled' => false],
                    ['content' => '10 mail02.test.nl.', 'disabled' => false],
                ],
            ],
        ],
    ];

    public function testCreateSingleResourceRecord(): void
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

    public function testCreateSingleResourceRecordFromClass(): void
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
        $rr = (new ResourceRecord())
            ->setName('test.test.nl.')
            ->setType(RecordType::A)
            ->setRecord('127.0.0.1')
            ->setTtl(10)
            ->setComments([
                (new Comment())->setContent('Hello')->setAccount('root')->setModifiedAt(1),
                (new Comment())->setContent('Power')->setAccount('admin')->setModifiedAt(2),
                (new Comment())->setContent('DNS')->setAccount('superuser')->setModifiedAt(3),
            ]);
        $zone->create($rr);
    }

    public function testCreateMultipleResourceRecords(): void
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

    public function testCreateMultipleResourceRecordsFromClass(): void
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
            (new ResourceRecord())
                ->setName('test')
                ->setType(RecordType::A)
                ->setRecord((new Record())
                    ->setContent('127.0.0.1'))
                ->setTtl(10),
            (new ResourceRecord())
                ->setName('@')
                ->setType(RecordType::A)
                ->setRecord((new Record())->setContent('127.0.0.1'))
                ->setTtl(20),
            (new ResourceRecord())
                ->setName('@')
                ->setType(RecordType::MX)
                ->setRecords([
                    (new Record())->setContent('10 mail01.test.nl.'),
                    (new Record())->setContent('20 mail02.test.nl.'),
                ])
                ->setTtl(30),
            (new ResourceRecord())
                ->setName('test02')
                ->setType(RecordType::A)
                ->setRecord((new Record())->setContent('127.0.0.1'))
                ->setTtl(40),

            (new ResourceRecord())
                ->setName('test03')
                ->setType(RecordType::TXT)->setRecord((new Record())->setContent('"v=DMARC1; p=none; rua=mailto:info@test.nl; ruf=mailto:info@test.nl"'))
                ->setTtl(40),
        ]);
    }

    public function testCreateNoResourceRecords(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('patch')->once()->withArgs(['zones/test.nl.', Mockery::on(function (RRSetTransformer $transformer) {
            $data = $transformer->transform();

            $this->assertEmpty($data->rrsets);

            return true;
        })]);

        $zone = new Zone($connector, 'test.nl');
        $zone->create([]);
    }

    public function testGetResourceRecords(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('get')->withArgs(['zones/test.nl.'])->once()->andReturn(self::API_RESPONSE);

        $zone = new Zone($connector, 'test.nl');

        $this->assertSame(2, $zone->get()->count());
        $this->assertSame(1, $zone->get('MX')->count());
    }

    public function testFindResourceRecords(): void
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
        $this->assertSame('test.nl.', $zone->getCanonicalName());
    }

    public function testSetNsec3param(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('put')->withArgs(['zones/test.nl.', Mockery::on(function ($transformer) {
            $transformed = $transformer->transform();
            $this->assertSame('1 0 0 f00bar', $transformed->nsec3param);

            return true;
        })])->once()->andReturn([]);
        $zone = Mockery::mock(Zone::class.'[resource]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('resource')->withNoArgs()->once()->andReturn(new ZoneResource());

        $zone->setNsec3param('1 0 0 f00bar');
    }

    public function testSetEmptyNsec3param(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('put')->withArgs(['zones/test.nl.', Mockery::on(function ($transformer) {
            $transformed = $transformer->transform();
            $this->assertEmpty($transformed->nsec3param);

            return true;
        })])->once()->andReturn([]);
        $zone = Mockery::mock(Zone::class.'[resource]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('resource')->withNoArgs()->once()->andReturn(new ZoneResource());

        $zone->setNsec3param(null);
    }

    public function testUnsetNsec3param(): void
    {
        $connector = Mockery::mock(Connector::class);
        $zone = Mockery::mock(Zone::class.'[resource,setNsec3param]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('resource')->withNoArgs()->once()->andReturn(new ZoneResource());
        $zone->shouldReceive('setNsec3param')->withArgs([null])->once()->andReturnTrue();

        $this->assertTrue($zone->unsetNsec3param());
    }

    public function testSetDnssec(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('put')->withArgs(['zones/test.nl.', Mockery::on(function ($transformer) {
            $transformed = $transformer->transform();
            $this->assertTrue($transformed->dnssec);

            return true;
        })])->once()->andReturn([]);
        $zone = Mockery::mock(Zone::class.'[resource]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('resource')->withNoArgs()->once()->andReturn(new ZoneResource());

        $zone->setDnssec(true);
    }

    public function testEnableDnssec(): void
    {
        $connector = Mockery::mock(Connector::class);
        $zone = Mockery::mock(Zone::class.'[setDnssec]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('setDnssec')->withArgs([true])->once()->andReturnTrue();

        $this->assertTrue($zone->enableDnssec());
    }

    public function testDisableDnssec(): void
    {
        $connector = Mockery::mock(Connector::class);
        $zone = Mockery::mock(Zone::class.'[setDnssec]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('setDnssec')->withArgs([false])->once()->andReturnTrue();

        $this->assertTrue($zone->disableDnssec());
    }

    public function testNotify(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('put')->withArgs(['zones/test.nl./notify'])->once()->andReturn([]);

        $zone = new Zone($connector, 'test.nl');
        $this->assertTrue($zone->notify());
    }

    public function testSetKind(): void
    {
        $connector = Mockery::mock(Connector::class);
        $connector->shouldReceive('put')->withArgs(['zones/test.nl.', Mockery::on(function ($transformer) {
            $transformed = $transformer->transform();
            $this->assertSame('Slave', $transformed->kind);
            $this->assertSame(['1.1.1.1'], $transformed->masters);

            return true;
        })])->once()->andReturn([]);
        $zone = Mockery::mock(Zone::class.'[resource]', [$connector, 'test.nl'])->makePartial();
        $zone->shouldReceive('resource')->withNoArgs()->once()->andReturn(new ZoneResource());

        $zone->setKind('Slave', ['1.1.1.1']);
    }
}
