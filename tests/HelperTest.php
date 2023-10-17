<?php

namespace Exonet\Powerdns\tests;

use Exonet\Powerdns\Helper;
use Exonet\Powerdns\RecordType;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class HelperTest extends TestCase
{
    public function testWithArguments(): void
    {
        $result = Helper::createResourceRecord('unit.test.', 'www', RecordType::A, '127.0.0.1', 1337, [['content' => 'Hello World', 'account' => 'Tester']]);

        self::assertSame('www.unit.test.', $result->getName());
        self::assertSame('A', $result->getType());
        self::assertSame(1337, $result->getTtl());
        self::assertCount(1, $result->getRecords());
        self::assertSame('127.0.0.1', $result->getRecords()[0]->getContent());
        self::assertSame('Hello World', $result->getComments()[0]->getContent());
    }

    public function testWithArray(): void
    {
        $result = Helper::createResourceRecord(
            'unit.test.',
            [
                'name' => '@',
                'type' => RecordType::A,
                'content' => ['127.0.0.1', '127.0.0.2'],
                'ttl' => 1337,
                'comments' => [
                    [
                        'content' => "Hello",
                        'account' => 'rooti',
                        'modified_at' => 999
                    ],
                    [
                        'content' => "World",
                        'account' => 'rooti',
                        'modified_at' => 111
                    ]
                ]
            ]
        );

        self::assertSame('unit.test.', $result->getName());
        self::assertSame('A', $result->getType());
        self::assertSame(1337, $result->getTtl());
        self::assertCount(2, $result->getRecords());
        self::assertSame('127.0.0.1', $result->getRecords()[0]->getContent());
        self::assertSame('127.0.0.2', $result->getRecords()[1]->getContent());
        self::assertSame(111, $result->getComments()[1]->getModifiedAt());
    }

    public function testWithApiResponse(): void
    {
        foreach (ZoneTest::API_RESPONSE['rrsets'] as $rrset) {
            $results[] = Helper::createResourceRecord('test.nl.', $rrset);
        }

        self::assertCount(2, $results);

        self::assertSame('record01.test.nl.', $results[0]->getName());
        self::assertSame('A', $results[0]->getType());
        self::assertSame(3600, $results[0]->getTtl());
        self::assertCount(1, $results[0]->getRecords());
        self::assertSame('127.0.0.1', $results[0]->getRecords()[0]->getContent());

        self::assertSame('record02.test.nl.', $results[1]->getName());
        self::assertSame('MX', $results[1]->getType());
        self::assertSame(3600, $results[1]->getTtl());
        self::assertCount(1, $results[0]->getRecords());
        self::assertSame('10 mail01.test.nl.', $results[1]->getRecords()[0]->getContent());
        self::assertSame('10 mail02.test.nl.', $results[1]->getRecords()[1]->getContent());
    }
}
