<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\TSIGKey as TSIGKeyResource;
use Exonet\Powerdns\Resources\TSIGKeySet;
use Exonet\Powerdns\Transformers\TSIGKeyTransformer;

class TSIGKey extends AbstractZone
{
    /**
     * Get all tsigkeys on the server.
     *
     * @return TSIGKeySet The meta data set.
     */
    public function getAll(): TSIGKeySet
    {
        $items = $this->connector->get('tsigkeys');

        $resultSet = new TSIGKeySet();
        foreach ($items as $item) {
            $resultSet->addResource(new TSIGKeyResource($item));
        }

        return $resultSet;
    }

    /**
     * Get a single tsigkey.
     *
     * @param string $id the id
     *
     * @return TSIGKeyResource The meta data set.
     */
    public function get(string $id): TSIGKeyResource
    {
        $item = $this->connector->get('tsigkeys/'.$id);

        return new TSIGKeyResource($item);
    }

    /**
     * Creat a new TSIG Key.
     *
     * @param array|string $data The data.
     *
     * @return TSIGKeySet The created key data set.
     */
    public function create(TSIGKeyResource $data): TSIGKeySet
    {
        $response = $this->connector->post('tsigkeys', new TSIGKeyTransformer($data));

        return new TSIGKeySet([new TSIGKeyResource($response)]);
    }

    /**
     * Update an existing tsig key.
     *
     * @param TSIGKeyResource $key The key data item to update.
     *
     * @return bool True if the update was successful.
     */
    public function update(TSIGKeyResource $key): bool
    {
        $response = $this->connector->put('tsigkeys/'.$key->getId(), new TSIGKeyTransformer($key));

        // If the response is empty, everything is fine.
        return empty($response);
    }

    /**
     * Delete an existing tsigkey item.
     *
     * @param TSIGKeyResource $key The tsigkey data item to delete.
     *
     * @return bool True if the delete was successful.
     */
    public function delete(TSIGKeyResource $key): bool
    {
        $response = $this->connector->delete('tsigkeys/'.$key->getId());

        // If the response is empty, everything is fine.
        return empty($response);
    }
}
