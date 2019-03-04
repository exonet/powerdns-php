<?php

namespace Exonet\Powerdns\Transformers;

class CryptokeyActiveTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'active' => $this->data['active'],
        ];
    }
}
