<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

use ArrayAccess;
use ArrayIterator;
use Closure;
use IteratorAggregate;

class SearchResultSet implements IteratorAggregate, ArrayAccess
{
    /**
     * @var SearchResult[] Array containing the results.
     */
    private $searchResults = [];

    /**
     * SearchResultSet constructor.
     *
     * @param array|null $searchResults (Optional) The search results to add.
     */
    public function __construct(?array $searchResults = null)
    {
        $this->searchResults = $searchResults ?? [];
    }

    /**
     * Add a single search result to the existing collection.
     *
     * @param SearchResult $searchResult The search result to add.
     *
     * @return SearchResultSet The current SearchResultSet instance.
     */
    public function addResult(SearchResult $searchResult): self
    {
        $this->searchResults[] = $searchResult;

        return $this;
    }

    /**
     * Get the number of search results in this collection.
     *
     * @return int The number of search results.
     */
    public function count(): int
    {
        return count($this->searchResults);
    }

    /**
     * Check if the current collection is not empty.
     *
     * @return bool True when there are search results in this collection.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Check if the current collection is empty.
     *
     * @return bool True when there are no search results in this collection.
     */
    public function isEmpty(): bool
    {
        return empty($this->searchResults);
    }

    /**
     * Loop through the collection and call the given closure for each search result.
     *
     * @param Closure $closure The closure to execute for each search result.
     *
     * @return SearchResultSet The current SearchResultSet instance.
     */
    public function map(Closure $closure): self
    {
        foreach ($this->searchResults as $index => $resource) {
            $this->searchResults[$index] = $closure($resource, $index);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->searchResults);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->searchResults[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): SearchResult
    {
        return $this->searchResults[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->searchResults[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->searchResults[$offset]);
    }
}
