<?php

namespace Exonet\Powerdns\Transformers;

class SoaEditTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'soa_edit' => $this->data['soa_edit'],
        ];
    }
}
