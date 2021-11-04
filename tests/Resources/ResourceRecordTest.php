<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Exceptions\InvalidChangeType;
use Exonet\Powerdns\Exceptions\InvalidRecordType;
use Exonet\Powerdns\Resources\Comment;
use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;
use Exonet\Powerdns\Zone;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ResourceRecordTest extends TestCase
{
    public function testSimpleSettersAndGetters(): void
    {
        $resourceRecord = new ResourceRecord();

        $resourceRecord = $resourceRecord->setName('test name')->setTtl(1234);

        $this->assertSame('test name', $resourceRecord->getName());
        $this->assertSame(1234, $resourceRecord->getTtl());
    }

    public function testZoneRelatedMethods(): void
    {
        $resourceRecord = new ResourceRecord();
        $zone = Mockery::mock(Zone::class);
        $zone->shouldReceive('patch')->withArgs([[$resourceRecord]])->once()->andReturnTrue();
        $zone->shouldReceive('patch')->withArgs(
            [
                [
                    Mockery::on(
                        function (ResourceRecord $updatedResourceRecord) {
                            return $updatedResourceRecord->getChangeType() === 'DELETE';
                        }
                    ),
                ],
            ]
        )->once()->andReturnTrue();

        $resourceRecord = $resourceRecord->setZone($zone);

        $this->assertTrue($resourceRecord->save());
        $this->assertTrue($resourceRecord->delete());
    }

    public function testSetApiResponse(): void
    {
        $apiResponse =
            [
                'name' => 'record.test.nl.',
                'type' => 'A',
                'ttl' => 3600,
                'changetype' => 'REPLACE',
                'records' => [
                    ['content' => '127.0.0.1', 'disabled' => false],
                    ['content' => '127.0.0.2', 'disabled' => true],
                ],
                'comments' => [
                    ['content' => 'Test comment', 'account' => 'Test account', 'modified_at' => 1234],
                ],
            ];

        $resourceRecord = (new ResourceRecord())->setApiResponse($apiResponse);

        $this->assertSame('record.test.nl.', $resourceRecord->getName());
        $this->assertSame(3600, $resourceRecord->getTtl());
        $this->assertSame('A', $resourceRecord->getType());
        $this->assertSame('REPLACE', $resourceRecord->getChangeType());

        $records = $resourceRecord->getRecords();
        $this->assertInstanceOf(Record::class, $records[0]);
        $this->assertSame('127.0.0.1', $records[0]->getContent());
        $this->assertFalse($records[0]->isDisabled());

        $this->assertInstanceOf(Record::class, $records[1]);
        $this->assertSame('127.0.0.2', $records[1]->getContent());
        $this->assertTrue($records[1]->isDisabled());

        $comments = $resourceRecord->getComments();
        $this->assertInstanceOf(Comment::class, $comments[0]);
        $this->assertSame('Test comment', $comments[0]->getContent());
        $this->assertSame('Test account', $comments[0]->getAccount());
        $this->assertSame(1234, $comments[0]->getModifiedAt());
    }

    public function testInvalidChangeTypeThrowsException()
    {
        $resourceRecord = new ResourceRecord();

        $this->expectException(InvalidChangeType::class);
        $this->expectExceptionMessage('The change type [TEST] is invalid. This must either be "REPLACE" or "DELETE"');

        $resourceRecord->setChangeType('test');
    }

    public function testSetTypeWithInvalidTypeThrowsException()
    {
        $resourceRecord = new ResourceRecord();

        $this->expectException(InvalidRecordType::class);
        $this->expectExceptionMessage('The record type [TEST] is not a valid DNS Record type.');

        $resourceRecord->setType('test');
    }
}
