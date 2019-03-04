<?php

namespace Exonet\Powerdns\Transformers;

use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;

class RRSetTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        $transformedResourceRecords = [];

        foreach ($this->data as $resourceRecord) {
            $transformedResourceRecords[] = $this->transformResourceRecord($resourceRecord);
        }

        return (object) ['rrsets' => $transformedResourceRecords];
    }

    /**
     * Transform a resource record.
     *
     * @param ResourceRecord $resourceRecord The resource record to transform.
     *
     * @return object The transformed resource record.
     */
    private function transformResourceRecord(ResourceRecord $resourceRecord)
    {
        $recordList = [];
        foreach ($resourceRecord->getRecords() as $record) {
            $recordList[] = $this->transformRecord($record);
        }

        return (object) [
            'name' => $resourceRecord->getName(),
            'type' => $resourceRecord->getType(),
            'ttl' => $resourceRecord->getTtl(),
            'changetype' => $resourceRecord->getChangeType(),
            'records' => $recordList,
        ];
    }

    /**
     * Transform a record.
     *
     * @param Record $record The record to transform.
     *
     * @return object The transformed record.
     */
    private function transformRecord(Record $record)
    {
        return (object) [
            'content' => $record->getContent(),
            'set_ptr' => $record->isSetPtr(),
            'disabled' => $record->isDisabled(),
        ];
    }
}
