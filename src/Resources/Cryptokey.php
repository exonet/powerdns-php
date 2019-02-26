<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

class Cryptokey
{
    /**
     * @var string Set to "Cryptokey".
     */
    private $type;

    /**
     * @var int The internal identifier, read only.
     */
    private $id;

    /**
     * @var string The key type.
     */
    private $keyType;

    /**
     * @var bool Whether or not the key is in active use.
     */
    private $active;

    /**
     * @var string The DNSKEY record for this key.
     */
    private $dnsKey;

    /**
     * @var string[] An array of DS records for this key.
     */
    private $ds;

    /**
     * @var string The private key in ISC format.
     */
    private $privateKey;

    /**
     * @var string The name of the algorithm of the key, should be a mnemonic.
     */
    private $algorithm;

    /**
     * @var int The size of the key.
     */
    private $bits;

    /**
     * @var bool True when it is an existing crypto key record.
     */
    private $existingRecord = false;

    /**
     * Set the crypto key data based on the API response.
     *
     * @param mixed[] $cryptokey The API response.
     *
     * @return Cryptokey A fresh instance filled with data from the API.
     */
    public function setApiResponse(array $cryptokey) : self
    {
        // Create a fresh instance of this resource.
        $newClass = new static();

        if (isset($cryptokey['type']) && is_string($cryptokey['type'])) {
            $newClass->setType($cryptokey['type']);
        }

        if (isset($cryptokey['id']) && is_int($cryptokey['id'])) {
            $newClass->setId($cryptokey['id']);
        }

        if (isset($cryptokey['keytype']) && is_string($cryptokey['keytype'])) {
            $newClass->setKeyType($cryptokey['keytype']);
        }

        if (isset($cryptokey['active'])) {
            $newClass->setActive($cryptokey['active']);
        }

        if (isset($cryptokey['dnskey']) && is_string($cryptokey['dnskey'])) {
            $newClass->setDnsKey($cryptokey['dnskey']);
        }

        if (isset($cryptokey['ds']) && is_array($cryptokey['ds'])) {
            $newClass->setDs($cryptokey['ds']);
        }

        if (isset($cryptokey['privatekey']) && is_string($cryptokey['privatekey'])) {
            $newClass->setPrivateKey($cryptokey['privatekey']);
        }

        if (isset($cryptokey['algorithm']) && is_string($cryptokey['algorithm'])) {
            $newClass->setAlgorithm($cryptokey['algorithm']);
        }

        if (isset($cryptokey['bits']) && is_int($cryptokey['bits'])) {
            $newClass->setBits($cryptokey['bits']);
        }

        $newClass->existingRecord = true;

        return $newClass;
    }

    /**
     * The type.
     *
     * @return string The type.
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @param string $type Set to "Cryptokey".
     *
     * @return $this The current Cryptokey instance.
     */
    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the internal identifier, read only.
     *
     * @return int The internal identifier, read only.
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set the internal identifier, read only.
     *
     * @param int $id The internal identifier, read only.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setId(int $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the key type.
     *
     * @return string The key type.
     */
    public function getKeyType() : string
    {
        return $this->keyType;
    }

    /**
     * Set the key type.
     *
     * @param string $keyType The key type.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setKeyType(string $keyType) : self
    {
        $this->keyType = $keyType;

        return $this;
    }

    /**
     * Whether or not the key is in active use.
     *
     * @return bool True when active.
     */
    public function isActive() : bool
    {
        return $this->active;
    }

    /**
     * Set whether or not the key is in active use.
     *
     * @param bool $active True when active.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setActive(bool $active) : self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the DNSKEY record for this key.
     *
     * @return string The DNSKEY record for this key.
     */
    public function getDnsKey() : string
    {
        return $this->dnsKey;
    }

    /**
     * Set the DNSKEY record for this key.
     *
     * @param string $dnsKey The DNSKEY record for this key.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setDnsKey(string $dnsKey) : self
    {
        $this->dnsKey = $dnsKey;

        return $this;
    }

    /**
     * Get the array of DS records for this key.
     *
     * @return string[] An array of DS records for this key.
     */
    public function getDs() : array
    {
        return $this->ds;
    }

    /**
     * Set the array of DS records for this key.
     *
     * @param string[] $ds An array of DS records for this key.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setDs(array $ds) : self
    {
        $this->ds = $ds;

        return $this;
    }

    /**
     * Get the private key in ISC format.
     *
     * @return string The private key in ISC format.
     */
    public function getPrivateKey() : string
    {
        return $this->privateKey;
    }

    /**
     * Set the private key in ISC format.
     *
     * @param string $privateKey The private key in ISC format.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setPrivateKey(string $privateKey) : self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get the name of the algorithm of the key, should be a mnemonic.
     *
     * @return string The name of the algorithm of the key, should be a mnemonic.
     */
    public function getAlgorithm() : string
    {
        return $this->algorithm;
    }

    /**
     * Set the name of the algorithm of the key, should be a mnemonic.
     *
     * @param string $algorithm The name of the algorithm of the key, should be a mnemonic.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setAlgorithm(string $algorithm) : self
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get the size of the key.
     *
     * @return int The size of the key.
     */
    public function getBits() : int
    {
        return $this->bits;
    }

    /**
     * Set the size of the key.
     *
     * @param int $bits The size of the key.
     *
     * @return $this The current Cryptokey instance.
     */
    public function setBits(int $bits) : self
    {
        $this->bits = $bits;

        return $this;
    }
}
