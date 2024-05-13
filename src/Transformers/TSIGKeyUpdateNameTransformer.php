<?php

namespace Exonet\Powerdns\Transformers;

class TSIGKeyUpdateNameTransformer extends Transformer {
    /**
     * {@inheritdoc}
     */
    public function transform() {
        return (object) [
            'name' => $this->data->getName(),
        ];
    }
}
