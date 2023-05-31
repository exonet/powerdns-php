<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IteratorAggregate;

class MetaSet implements IteratorAggregate, ArrayAccess
{
    /**
     * @var Meta[] Array containing resources.
     */
    private $metaResources = [];

    /**
     * MetaSet constructor.
     *
     * @param array|null $resourceRecords (Optional) The meta resources to add.
     */
    public function __construct(?array $resourceRecords = null)
    {
        if ($resourceRecords) {
            $this->metaResources = $resourceRecords;
        }
    }

    /**
     * Add a single meta resource to the existing collection.
     *
     * @param Meta $metaResource The meta resource to add.
     *
     * @return MetaSet The current MetaSet instance.
     */
    public function addResource(Meta $metaResource): self
    {
        $this->metaResources[] = $metaResource;

        return $this;
    }

    /**
     * Get the number of meta resources in this collection.
     *
     * @return int The number of meta resources.
     */
    public function count(): int
    {
        return count($this->metaResources);
    }

    /**
     * Check if the current collection is not empty.
     *
     * @return bool True when there are meta resources in this collection.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Check if the current collection is empty.
     *
     * @return bool True when there are no meta resources in this collection.
     */
    public function isEmpty(): bool
    {
        return empty($this->metaResources);
    }

    /**
     * Loop through the collection and call the given closure for each meta resource.
     *
     * @param Closure $closure The closure to execute for each meta resource.
     *
     * @return MetaSet The current MetaSet instance.
     */
    public function map(Closure $closure): self
    {
        foreach ($this->metaResources as $index => $resource) {
            $this->metaResources[$index] = $closure($resource, $index);
        }

        return $this;
    }

    /**
     * Delete all meta resources that are set in the current collection.
     *
     * @return bool True when the meta resources are deleted.
     */
    public function delete(): bool
    {
        foreach ($this->metaResources as $resource) {
           $resource->delete();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->metaResources);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->metaResources[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->metaResources[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->metaResources[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->metaResources[$offset]);
    }
}
