<?php

namespace Exonet\Powerdns\Transformers;

class CreateZoneTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'name' => $this->data->getName(),
            'kind' => $this->data->getKind(),
            'dnssec' => $this->data->hasDnssec(),
            'api_rectify' => $this->data->hasAutoRectify(),
            'soa_edit_api' => $this->data->getSoaEditApi(),
            'masters' => $this->data->getMasters(),
            'nameservers' => $this->data->getNameservers(),
        ];
    }
}
