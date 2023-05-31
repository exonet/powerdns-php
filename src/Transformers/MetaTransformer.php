<?php

namespace Exonet\Powerdns\Transformers;

class MetaTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'kind' => $this->data->getKind(),
            'metadata' => $this->data->getData(),
            'type' => 'Metadata',
        ];
    }
}
