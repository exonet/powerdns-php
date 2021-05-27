<?php

namespace Exonet\Powerdns;

use Exonet\Powerdns\Resources\Cryptokey as CryptokeyResource;
use Exonet\Powerdns\Transformers\CryptokeyActiveTransformer;
use Exonet\Powerdns\Transformers\CryptokeyCreateTransformer;

class Cryptokey extends AbstractZone
{
    /**
     * @var CryptokeyCreateTransformer The (prepared) transformer.
     */
    private $createTransformer;

    /**
     * @var CryptokeyResource The Crypto Key resource class to use.
     */
    private $cryptoKeyResource;

    /**
     * Crypto Key constructor.
     *
     * @param ConnectorInterface     $connector         The zone to use.
     * @param null|string            $canonicalDomain   The Powerdns Connector to make calls.
     * @param CryptokeyResource|null $cryptoKeyResource The cryptokey resource class to use for API responses.
     */
    public function __construct(
        ConnectorInterface $connector,
        ?string $canonicalDomain = null,
        ?CryptokeyResource $cryptoKeyResource = null
    ) {
        parent::__construct($connector, $canonicalDomain);

        $this->cryptoKeyResource = $cryptoKeyResource ?? new CryptokeyResource();
    }

    /**
     * Set the private key to use.
     *
     * @param string $keyContent The key.
     *
     * @return $this The current class instance.
     */
    public function setPrivateKey(string $keyContent): self
    {
        $this->createTransformer = new CryptokeyCreateTransformer(['content' => $keyContent]);

        return $this;
    }

    /**
     * Configure the parameters for generating a new private key.
     *
     * @param string   $algorithm The algorithm to use.
     * @param int|null $bits      The length of the key.
     * @param string   $keyType   The key type. Can be 'ksk', 'zsk' or 'csk'.
     *
     * @return $this The current class instance.
     */
    public function configurePrivateKey(string $algorithm, ?int $bits = null, string $keyType = 'csk'): self
    {
        $this->createTransformer = new CryptokeyCreateTransformer([
            'algorithm' => $algorithm,
            'bits' => $bits,
            'keytype' => $keyType,
        ]);

        return $this;
    }

    /**
     * Create a new Crypto Key. By default the key is not enabled.
     *
     * @param bool   $active  When true, create and activate this key.
     * @param string $keyType The key type. Can be 'ksk', 'zsk' or 'csk'.
     *
     * @return CryptokeyResource The created Crypto Key.
     */
    public function create(bool $active = false, string $keyType = 'csk'): CryptokeyResource
    {
        // Check if a transformer is already initialized. If it isn,t create one.
        if ($this->createTransformer === null) {
            // Initialize the transformer if it isn't set.
            $transformer = new CryptokeyCreateTransformer(['active' => $active, 'keytype' => $keyType]);
        } else {
            // Grab the current transformer and add the 'active' boolean.
            $transformer = $this->createTransformer->setData(array_merge(
                (array) $this->createTransformer->transform(),
                ['active' => $active]
            ));
        }

        // Create the Crypto Key.
        return $this->cryptoKeyResource->setApiResponse(
            $this->connector->post($this->getZonePath('/cryptokeys'), $transformer)
        );
    }

    /**
     * Get all active Crypto Keys.
     *
     * @return CryptokeyResource[] The active keys.
     */
    public function getActiveKeys(): array
    {
        return $this->keysByActive(true);
    }

    /**
     * Get all inactive Crypto Keys.
     *
     * @return CryptokeyResource[] The inactive keys.
     */
    public function getInactiveKeys(): array
    {
        return $this->keysByActive(false);
    }

    /**
     * Delete all Crypto Keys that are inactive.
     *
     * @return int[] The IDs of the keys that are deleted.
     */
    public function deleteInactive(): array
    {
        $keys = $this->keysByActive(false, true);

        return call_user_func_array([$this, 'deleteKeys'], $keys);
    }

    /**
     * Delete the crypto key with the given ID(s).
     *
     * @param int|int[] $keyIds The key ID(s) to remove.
     *
     * @return int[] The IDs of the keys that are deleted.
     */
    public function deleteKeys(int ...$keyIds): array
    {
        $deletedKeys = [];

        foreach ($keyIds as $id) {
            $this->connector->delete($this->getZonePath(sprintf('/cryptokeys/%s', $id)));

            $deletedKeys[] = $id;
        }

        return $deletedKeys;
    }

    /**
     * Get all keys for this zone.
     *
     * @param bool $includePrivateKey When true, include the private key.
     *
     * @return CryptokeyResource[] Array with Crypto Key resources.
     */
    public function getKeys(bool $includePrivateKey = false): array
    {
        $data = $this->connector->get($this->getZonePath('/cryptokeys'));

        $keys = [];
        foreach ($data as $item) {
            if ($includePrivateKey === false) {
                $keys[] = $this->cryptoKeyResource->setApiResponse($item);

                continue;
            }

            // Private keys are only retrieved when getting single keys.
            $keyDetails = $this->connector->get($this->getZonePath(sprintf('/cryptokeys/%d', $item['id'])));
            $keys[] = $this->cryptoKeyResource->setApiResponse($keyDetails);
        }

        return $keys;
    }

    /**
     * (De)activate all Crypto Keys for the current zone. If no $keyIds are given, all keys will be updated.
     *
     * @param bool  $active True to active all keys, false to deactivate them.
     * @param int[] $keyIds The ID of the keys to update. When empty or omitted, all keys will be updated.
     *
     * @return int[] IDs of the keys that are (de)activated.
     */
    public function setActive(bool $active, int ...$keyIds): array
    {
        $keyIds = !empty($keyIds) ? $keyIds : $this->keysByActive(!$active, true);
        $activatedKeys = [];

        foreach ($keyIds as $keyId) {
            $this->connector->put(
                $this->getZonePath(sprintf('/cryptokeys/%d', $keyId)),
                new CryptokeyActiveTransformer(['active' => $active])
            );

            $activatedKeys[] = $keyId;
        }

        return $activatedKeys;
    }

    /**
     * Get a list of Crypto Key resources or IDs with a specific state.
     *
     * @param bool $active   The state the key must have.
     * @param bool $asIdList Default: false. When true, return only the IDs.
     *
     * @return CryptokeyResource[]|int[] An array with Crypto Key resources or IDs.
     */
    private function keysByActive(bool $active, bool $asIdList = false): array
    {
        $keys = $this->getKeys();
        $foundKeys = [];

        foreach ($keys as $key) {
            if ($key->isActive() === $active) {
                $foundKeys[] = $asIdList ? $key->getId() : $key;
            }
        }

        return $foundKeys;
    }
}
