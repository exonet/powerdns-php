<?php

namespace Exonet\Powerdns\tests\Resources;

use Exonet\Powerdns\Resources\Record;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RecordTest extends TestCase
{
    public function testSettersAndGetters()
    {
        $record = new Record();

        $record = $record->setContent('test content')->setDisabled(true)->setSetPtr(true);

        $this->assertSame('test content', $record->getContent());
        $this->assertTrue($record->isDisabled());
        $this->assertTrue($record->isSetPtr());
    }

    public function testContentViaConstruct()
    {
        $record = new Record('test content');

        $this->assertSame('test content', $record->getContent());
    }
}
