<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\Comment;
use Exonet\Powerdns\Resources\Record;
use Exonet\Powerdns\Resources\ResourceRecord;

class Helper
{
    /**
     * Make a new resource record based on the arguments or an array with data.
     *
     * @param string       $zoneName The zone name for this resource record.
     * @param array|string $name     The resource record name or an array with the data.
     * @param string       $type     The type of the resource record.
     * @param array|string $content  The content of the resource record.
     * @param int          $ttl      The TTL.
     * @param array        $comments The Comment.
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
        int $ttl = 3600,
        array $comments = []
    ): ResourceRecord {
        if (is_array($name)) {
            if (isset($name['records'])) {
                return (new ResourceRecord())->setApiResponse($name);
            }

            $comments = $name['comments'] ?? [];
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

        if (is_array($comments)) {
            $commentList = [];
            foreach ($comments as $comment) {
                // we can use an empty fallback for account and nothing the current time for modified_at
                $commentList[] = (new Comment())
                    ->setContent($comment['content'])
                    ->setAccount($comment['account'] ?? '')
                    ->setModifiedAt($comment['modified_at'] ?? time());
            }
            $resourceRecord->setComments($commentList);
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
