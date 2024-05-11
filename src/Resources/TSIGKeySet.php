<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IteratorAggregate;
use ReturnTypeWillChange;

class TSIGKeySet implements IteratorAggregate, ArrayAccess {
    /**
     * @var TSIGKey[] Array containing resources.
     */
    private $tsigResources = [];

    /**
     * TSIGKeySet constructor.
     *
     * @param array|null $resourceRecords (Optional) The tsigkey resources to add.
     */
    public function __construct(?array $resourceRecords = null) {
        if ($resourceRecords) {
            $this->tsigResources = $resourceRecords;
        }
    }

    /**
     * Add a single tsigkey resource to the existing collection.
     *
     * @param TSIGKey $metaResource The tsigkey resource to add.
     *
     * @return TSIGKeySet The current TSIGKeySet instance.
     */
    public function addResource(TSIGKey $metaResource): self {
        $this->tsigResources[] = $metaResource;

        return $this;
    }

    /**
     * Get the number of tsigkey resources in this collection.
     *
     * @return int The number of tsigkey resources.
     */
    public function count(): int {
        return count($this->tsigResources);
    }

    /**
     * Check if the current collection is not empty.
     *
     * @return bool True when there are tsigkey resources in this collection.
     */
    public function isNotEmpty(): bool {
        return !$this->isEmpty();
    }

    /**
     * Check if the current collection is empty.
     *
     * @return bool True when there are no tsigkey resources in this collection.
     */
    public function isEmpty(): bool {
        return empty($this->tsigResources);
    }

    /**
     * Loop through the collection and call the given closure for each tsigkey resource.
     *
     * @param Closure $closure The closure to execute for each tsigkey resource.
     *
     * @return TSIGKeySet The current TSIGKeySet instance.
     */
    public function map(Closure $closure): self {
        foreach ($this->tsigResources as $index => $resource) {
            $this->tsigResources[$index] = $closure($resource, $index);
        }

        return $this;
    }

    /**
     * Delete all tsigkey resources that are set in the current collection.
     *
     * @return bool True when the tsigkey resources are deleted.
     */
    public function delete(): bool {
        foreach ($this->tsigResources as $resource) {
            $resource->delete();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->tsigResources);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool {
        return isset($this->tsigResources[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @return TSIGKey
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset) {
        return $this->tsigResources[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void {
        $this->tsigResources[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void {
        unset($this->tsigResources[$offset]);
    }
}
