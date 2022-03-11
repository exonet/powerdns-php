<?php

namespace Exonet\Powerdns\Transformers;

class SoaEditApiTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'soa_edit_api' => $this->data['soa_edit_api'],
        ];
    }
}
