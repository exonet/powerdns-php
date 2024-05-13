<?php

namespace Exonet\Powerdns\Transformers;

abstract class Transformer {
    /**
     * @var mixed[] Array holding the data to transform.
     */
    protected $data = [];

    /**
     * Transformer constructor.
     *
     * @param null $data (optional) The data to transform.
     */
    public function __construct($data = null) {
        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * Set the data to transform.
     *
     * @param mixed $data The data to transform.
     *
     * @return $this The current transformer instance.
     */
    public function setData($data): self {
        $this->data = $data;

        return $this;
    }

    /**
     * Transform the data.
     *
     * @return object The transformed data.
     */
    abstract public function transform();
}
