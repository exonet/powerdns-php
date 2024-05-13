<?php

namespace Exonet\Powerdns\Transformers;

class TSIGKeyUpdateAlgorithmTransformer extends Transformer {
    /**
     * {@inheritdoc}
     */
    public function transform() {
        return (object) [
            'algorithm' => $this->data->getAlgorithm(),
        ];
    }
}
