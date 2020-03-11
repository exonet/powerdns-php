<?php

namespace Exonet\Powerdns\Transformers;

class Nsec3paramTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'nsec3param' => $this->data->getNsec3param(),
        ];
    }
}
