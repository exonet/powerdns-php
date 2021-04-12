<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;

class Helper
{
    /**
     * Make a new resource record based on the arguments or an array with data.
     *
     * @param string|array $name    The resource record name or an array with the data.
     * @param string       $type    The type of the resource record.
     * @param string       $content The content of the resource record.
     * @param int          $ttl     The TTL.
     *
     * @throws Exceptions\InvalidRecordType If the given type is invalid.
     *
     * @return ResourceRecord The constructed ResourceRecord.
     */
    public static function createResourceRecord(
        string $zoneName,
        $name,
        string $type = '',
        $content = '',
        int $ttl = 3600
    ): ResourceRecord {
        if (is_array($name)) {
            if (isset($name['records'])) {
                return (new ResourceRecord())->setApiResponse($name);
            }

            ['name' => $name, 'type' => $type, 'ttl' => $ttl, 'content' => $content] = $name;
        }

        $name = str_replace('@', $zoneName, $name);

        // If the name of the record doesn't end in the zone name, append the zone name to it.
        if (substr($name, -strlen($zoneName)) !== $zoneName) {
            $name = sprintf('%s.%s', $name, $zoneName);
        }

        $resourceRecord = new ResourceRecord();
        $resourceRecord
            ->setChangeType('replace')
            ->setName($name)
            ->setType($type)
            ->setTtl($ttl);

        if (is_string($content)) {
            $content = [$content];
        }

        $recordList = [];
        foreach ($content as $record) {
            $recordItem = new Record();

            if (is_string($record)) {
                $recordItem->setContent($record);
            } else {
                $recordItem->setContent($record['content']);
                if (isset($record['disabled'])) {
                    $recordItem->setDisabled($record['disabled']);
                }
            }

            $recordList[] = $recordItem;
        }

        $resourceRecord->setRecords($recordList);

        return $resourceRecord;
    }
}
