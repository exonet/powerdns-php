<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use Exonet\Powerdns\Exceptions\InvalidChangeType;
use Exonet\Powerdns\Exceptions\InvalidRecordType;
use Exonet\Powerdns\RecordType;
use Exonet\Powerdns\Zone;
use ReflectionClass;

class ResourceRecord
{
    /**
     * @var string MUST be added when updating the RRSet. Must be REPLACE or DELETE. With DELETE, all existing RRs
     *             matching name and type will be deleted, including all comments. With REPLACE: when records is present, all
     *             existing RRs matching name and type will be deleted, and then new records given in records will be created.
     *             If no records are left, any existing comments will be deleted as well. When comments is present, all
     *             existing comments for the RRs matching name and type will be deleted, and then new comments given in
     *             comments will be created.
     */
    private $changeType = 'REPLACE';

    /**
     * @var Comment[] List of Comment. Must be empty when changetype is set to DELETE. An empty list results in deletion
     *                of all comments.
     */
    private $comments;

    /**
     * @var string Name for record set.
     */
    private $name;

    /**
     * @var Record[] All records in this RRSet. When updating Records, this is the list of new records (replacing the
     *               old ones). Must be empty when changetype is set to DELETE. An empty list results in deletion of all records
     *               (and comments).
     */
    private $records;

    /**
     * @var int DNS TTL of the records, in seconds.
     */
    private $ttl;

    /**
     * @var string Type of this record (e.g. "A", "PTR", "MX").
     */
    private $type;

    /**
     * @var Zone The zone of this resource.
     */
    private $zone;

    /**
     * @var bool If true then this record is based on an existing record from an API response.
     */
    private $existingRecord = false;

    /**
     * Set the zone of this resource record.
     *
     * @param Zone $zone The zone.
     *
     * @return $this The current resource record.
     */
    public function setZone(Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * POST or PATCH the current resource record to the PowerDNS server.
     *
     * @return bool True when successful.
     */
    public function save(): bool
    {
        return $this->zone->patch([$this]);
    }

    /**
     * Delete the current resource record from the PowerDNS server.
     *
     * @return bool True when successful.
     */
    public function delete(): bool
    {
        $this->setChangeType('DELETE');

        return $this->save();
    }

    /**
     * Set the resource record data based on the API response.
     *
     * @param mixed[] $rrset The API response 'rrset'.
     *
     * @return $this The current resource record instance.
     */
    public function setApiResponse(array $rrset): self
    {
        if (isset($rrset['comments']) && is_array($rrset['comments'])) {
            foreach ($rrset['comments'] as $record) {
                $this->addComment(
                    (new Comment())
                        ->setContent($record['content'])
                        ->setAccount($record['account'])
                        ->setModifiedAt($record['modified_at'])
                );
            }
        }

        if (isset($rrset['records']) && is_array($rrset['records'])) {
            foreach ($rrset['records'] as $record) {
                $this->addRecord(
                    (new Record())
                        ->setContent($record['content'])
                        ->setDisabled($record['disabled'])
                );
            }
        }

        if (isset($rrset['name']) && is_string($rrset['name'])) {
            $this->setName($rrset['name']);
        }

        if (isset($rrset['ttl']) && is_int($rrset['ttl'])) {
            $this->setTtl($rrset['ttl']);
        }

        if (isset($rrset['type']) && is_string($rrset['type'])) {
            $this->setType($rrset['type']);
        }

        $this->existingRecord = true;

        return $this;
    }

    /**
     * Add a new comment.
     *
     * @param Comment $comment The comment.
     *
     * @return $this The current resource record instance.
     */
    public function addComment(Comment $comment): self
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Add a new record.
     *
     * @param Record $record The record.
     *
     * @return $this The current resource record instance.
     */
    public function addRecord(Record $record): self
    {
        $this->records[] = $record;

        return $this;
    }

    /**
     * Get the change type.
     *
     * @return string The current set change type.
     */
    public function getChangeType(): string
    {
        return $this->changeType;
    }

    /**
     * Set the change type to 'REPLACE' or 'DELETE'.
     *
     * @param string $changeType The desired change type.
     *
     * @throws InvalidChangeType If the given change type is invalid.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setChangeType(string $changeType): self
    {
        $changeType = strtoupper($changeType);
        if ($changeType === 'REPLACE' || $changeType === 'DELETE') {
            $this->changeType = $changeType;

            return $this;
        }

        throw new InvalidChangeType(
            sprintf('The change type [%s] is invalid. This must either be "REPLACE" or "DELETE"', $changeType)
        );
    }

    /**
     * Get all comments.
     *
     * @return Comment[] The comments.
     */
    public function getComments(): array
    {
        return $this->comments ?? [];
    }

    /**
     * Set comments. Will overwrite existing comments!
     *
     * @param Comment[] $comments The comments.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setComments(array $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get the name of the resource record.
     *
     * @return string The name.
     */
    public function getName(bool $short = false): string
    {
        $name = $this->name;

        if ($short) {
            $name = substr($this->name, 0, -(strlen($this->zone->getCanonicalName()) + 1));

            if (strlen($name) == 0) {
                $name = '@';
            }
        }

        return $name;
    }

    /**
     * Set the name of the resource record.
     *
     * @param string $name The name.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get all records of this resource record.
     *
     * @return Record[] The records.
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * Set a single record. Will replace existing records!
     *
     * @param Record|string $record The record.
     *
     * @return $this The current resource record instance.
     */
    public function setRecord($record): self
    {
        return $this->setRecords([$record]);
    }

    /**
     * Set the given records. Will replace existing records!
     *
     * @param Record[]|string[] $records The records.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setRecords(array $records): self
    {
        $recordList = [];

        foreach ($records as $record) {
            if (is_string($record)) {
                $record = new Record($record);
            }

            $recordList[] = $record;
        }

        $this->records = $recordList;

        return $this;
    }

    /**
     * Get the TTL.
     *
     * @return int The TTL.
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * Set the TTL.
     *
     * @param int $ttl The TTL in seconds.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Get the resource record type.
     *
     * @return string The resource record type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the resource record type. Can only be set if it isn't an existing record from the PowerDNS API. The reason
     * for this is that changing the type of an existing record will add a new record, instead of updating the
     * existing record.
     *
     * @param string $type The type.
     *
     * @throws InvalidRecordType If the record type can not be changed or is invalid.
     *
     * @return $this The current ResourceRecord instance.
     */
    public function setType(string $type): self
    {
        $type = strtoupper($type);

        if ($this->existingRecord && $type !== $this->getType()) {
            throw new InvalidRecordType(
                'Changing the type of existing DNS resource records can yield unexpected results and is not supported.',
                ['name' => $this->getName(), 'type' => $this->getType(), 'new_type' => $type]
            );
        }

        if ((new ReflectionClass(RecordType::class))->getConstant($type) !== false) {
            $this->type = $type;

            return $this;
        }

        throw new InvalidRecordType(sprintf('The record type [%s] is not a valid DNS Record type.', $type));
    }

    /**
     * Get the zone object for this ResourceRecord.
     *
     * @return Zone The zone for this ResourceRecord.
     */
    public function getZone(): Zone
    {
        return $this->zone;
    }
}
