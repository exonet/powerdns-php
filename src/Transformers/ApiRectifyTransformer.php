<?php

namespace Exonet\Powerdns\Transformers;

class ApiRectifyTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'api_rectify' => $this->data['api_rectify'],
        ];
    }
}
