<?php

namespace Exonet\Powerdns\Transformers;

class CryptokeyCreateTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'keytype' => $this->data['keytype'] ?? 'ksk',
            'content' => $this->data['content'] ?? null,
            'bits' => $this->data['bits'] ?? null,
            'algorithm' => $this->data['algorithm'] ?? null,
            'active' => $this->data['active'] ?? false,
        ];
    }
}
