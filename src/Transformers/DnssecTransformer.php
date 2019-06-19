<?php

namespace Exonet\Powerdns\Transformers;

class DnssecTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'dnssec' => $this->data['dnssec'],
        ];
    }
}
