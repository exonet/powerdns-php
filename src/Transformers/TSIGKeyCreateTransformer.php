<?php

namespace Exonet\Powerdns\Transformers;

class TSIGKeyCreateTransformer extends Transformer {
    /**
     * {@inheritdoc}
     */
    public function transform() {
        return (object) [
            'name'      => $this->data->getName(),
            'id'        => $this->data->getId(),
            'algorithm' => $this->data->getAlgorithm(),
            'key'       => $this->data->getKey(),
            'type'      => $this->data->getType(),
        ];
    }
}
