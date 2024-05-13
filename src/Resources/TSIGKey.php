<?php

declare(strict_types=1);

namespace Exonet\Powerdns\Resources;

class TSIGKey {
    /**
     * The name of the key.
     *
     * @var string
     */
    private $name;

    /**
     * The ID for this key, used in the TSIGkey URL endpoint.
     *
     * @var string
     */
    private $id;

    /**
     * The algorithm of the TSIG key.
     *
     * @var string
     */
    private $algorithm;

    /**
     * The Base64 encoded secret key, empty when listing keys. MAY be empty when POSTing to have the server generate the key material.
     *
     * @var string
     */
    private $key;

    /**
     * Set to "TSIGKey".
     *
     * @var string
     */
    private $type = 'TSIGKey';

    /**
     * Record constructor.
     *
     * @param string $content Optional content to set.
     */
    public function __construct(?array $content = null) {
        if ($content) {
            $this->setName($content['name'] ?? '');
            $this->setId($content['id'] ?? '');
            $this->setAlgorithm($content['algorithm'] ?? '');
            $this->setKey($content['key'] ?? '');
            $this->setType('TSIGKey');
        }
    }

    /**
     * Get set to "TSIGKey".
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set set to "TSIGKey".
     *
     * @param string $type Set to "TSIGKey"
     *
     * @return self
     */
    public function setType(string $type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the Base64 encoded secret key, empty when listing keys. MAY be empty when POSTing to have the server generate the key material.
     *
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Set the Base64 encoded secret key, empty when listing keys. MAY be empty when POSTing to have the server generate the key material.
     *
     * @param string $key The Base64 encoded secret key, empty when listing keys. MAY be empty when POSTing to have the server generate the key material
     *
     * @return self
     */
    public function setKey(string $key) {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the algorithm of the TSIG key.
     *
     * @return string
     */
    public function getAlgorithm() {
        return $this->algorithm;
    }

    /**
     * Set the algorithm of the TSIG key.
     *
     * @param string $algorithm The algorithm of the TSIG key
     *
     * @return self
     */
    public function setAlgorithm(string $algorithm) {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Get the ID for this key, used in the TSIGkey URL endpoint.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the ID for this key, used in the TSIGkey URL endpoint.
     *
     * @param string $id The ID for this key, used in the TSIGkey URL endpoint.
     *
     * @return self
     */
    public function setId(string $id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the name of the key.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the name of the key.
     *
     * @param string $name The name of the key
     *
     * @return self
     */
    public function setName(string $name) {
        $this->name = $name;

        return $this;
    }
}
