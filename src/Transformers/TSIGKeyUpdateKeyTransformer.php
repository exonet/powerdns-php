<?php

namespace Exonet\Powerdns\Transformers;

class TSIGKeyUpdateKeyTransformer extends Transformer {
    /**
     * {@inheritdoc}
     */
    public function transform() {
        return (object) [
            'key' => $this->data->getKey(),
        ];
    }
}
