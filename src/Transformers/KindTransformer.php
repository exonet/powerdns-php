<?php

namespace Exonet\Powerdns\Transformers;

class KindTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        $result = (object) [
            'kind' => $this->data['kind'],
        ];
        if ('Slave' == $this->data['kind']) {
            $result->masters = $this->data['masters'];
        }

        return $result;
    }
}
