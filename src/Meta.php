<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Exceptions\InvalidMetaKind;
use Exonet\Powerdns\Exceptions\ReadOnlyException;
use Exonet\Powerdns\Resources\Meta as MetaResource;
use Exonet\Powerdns\Resources\MetaSet;
use Exonet\Powerdns\Transformers\MetaTransformer;

class Meta extends AbstractZone
{
    /**
     * Get all the meta data for this zone, or only the meta data for a specific kind.
     *
     * @param string|null $metaKind If specified, only the meta data for this kind will be returned.
     *
     * @throws InvalidMetaKind When the meta kind is invalid.
     *
     * @return MetaSet The meta data set.
     */
    public function get(string $metaKind = null): MetaSet
    {
        $items = $this->connector->get($this->getZonePath('/metadata/'.$metaKind));

        // Wrap the result in an array if it's a single item.
        if (isset($items['kind'])) {
            $items = [$items];
        }

        $resultSet = new MetaSet();
        foreach ($items as $item) {
            $resultSet->addResource(new MetaResource($item['kind'], $item['metadata'] ?? [], $this));
        }

        return $resultSet;
    }

    /**
     * Create a new meta data item.
     *
     * @param string       $kind The meta kind.
     * @param array|string $data The meta data.
     *
     * @throws InvalidMetaKind   When the meta kind is invalid.
     * @throws ReadOnlyException When the meta kind is read-only.
     *
     * @return MetaSet The created meta data set.
     */
    public function create(string $kind, $data): MetaSet
    {
        $this->checkReadOnly($kind);
        $response = $this->connector->post($this->getZonePath('/metadata'), new MetaTransformer(new MetaResource($kind, $data)));

        return new MetaSet([new MetaResource($response['kind'], $response['metadata'] ?? [], $this)]);
    }

    /**
     * Update an existing meta data item.
     *
     * @param MetaResource $meta The meta data item to update.
     *
     * @throws ReadOnlyException When the meta kind is read-only.
     *
     * @return bool True if the update was successful.
     */
    public function update(MetaResource $meta): bool
    {
        $this->checkReadOnly($meta->getKind());
        $response = $this->connector->put($this->getZonePath('/metadata/'.$meta->getKind()), new MetaTransformer($meta));

        // If the response is empty, everything is fine.
        return empty($response);
    }

    /**
     * Delete an existing meta data item.
     *
     * @param MetaResource $meta The meta data item to delete.
     *
     * @throws ReadOnlyException When the meta kind is read-only.
     *
     * @return bool True if the delete was successful.
     */
    public function delete(MetaResource $meta): bool
    {
        $this->checkReadOnly($meta->getKind());
        $response = $this->connector->delete($this->getZonePath('/metadata/'.$meta->getKind()));

        // If the response is empty, everything is fine.
        return empty($response);
    }

    /**
     * Check if the meta kind is read-only.
     *
     * @param string $kind The meta kind.
     *
     * @throws ReadOnlyException When the meta kind is read-only.
     */
    protected function checkReadOnly(string $kind): void
    {
        if (in_array($kind, MetaType::READ_ONLY)) {
            throw new ReadOnlyException(sprintf('The meta kind [%s] is read-only.', $kind));
        }
    }
}
