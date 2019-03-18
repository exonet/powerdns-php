<?php

namespace Exonet\Powerdns\Transformers;

class DnssecToggleTransformer extends Transformer
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
