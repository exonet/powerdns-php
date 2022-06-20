<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Exonet\Powerdns\Zone;
use IteratorAggregate;

class ResourceSet implements IteratorAggregate, ArrayAccess
{
    /**
     * @var ResourceRecord[] Array containing resources.
     */
    private $resourceRecords = [];

    /**
     * @var Zone The zone.
     */
    private $zone;

    /**
     * ResourceSet constructor.
     *
     * @param Zone       $zone            The zone that contains the given resource records.
     * @param array|null $resourceRecords (Optional) The resource records to add.
     */
    public function __construct(Zone $zone, ?array $resourceRecords = null)
    {
        $this->zone = $zone;

        if ($resourceRecords) {
            $this->resourceRecords = $resourceRecords;
        }
    }

    /**
     * Add a single resource record to the existing collection.
     *
     * @param ResourceRecord $resourceRecord The resource record to add.
     *
     * @return ResourceSet The current ResourceSet instance.
     */
    public function addResource(ResourceRecord $resourceRecord): self
    {
        $this->resourceRecords[] = $resourceRecord;

        return $this;
    }

    /**
     * Get the number of resource records in this collection.
     *
     * @return int The number of resource records.
     */
    public function count(): int
    {
        return count($this->resourceRecords);
    }

    /**
     * Check if the current collection is not empty.
     *
     * @return bool True when there are resource records in this collection.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Check if the current collection is empty.
     *
     * @return bool True when there are no resource records in this collection.
     */
    public function isEmpty(): bool
    {
        return empty($this->resourceRecords);
    }

    /**
     * Loop through the collection and call the given closure for each resource record.
     *
     * @param Closure $closure The closure to execute for each resource record.
     *
     * @return ResourceSet The current ResourceSet instance.
     */
    public function map(Closure $closure): self
    {
        foreach ($this->resourceRecords as $index => $resource) {
            $this->resourceRecords[$index] = $closure($resource, $index);
        }

        return $this;
    }

    /**
     * Delete all resource records that are set in the current collection.
     *
     * @return bool True when the resource records are deleted.
     */
    public function delete(): bool
    {
        foreach ($this->resourceRecords as $index => $resource) {
            $this->resourceRecords[$index] = $resource->setChangeType('DELETE');
        }

        return $this->save();
    }

    /**
     * Save all resource records that are set in the current collection.
     *
     * @return bool True when the resource records are saved.
     */
    public function save(): bool
    {
        return $this->zone->patch($this->resourceRecords);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->resourceRecords);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->resourceRecords[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->resourceRecords[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->resourceRecords[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->resourceRecords[$offset]);
    }
}
