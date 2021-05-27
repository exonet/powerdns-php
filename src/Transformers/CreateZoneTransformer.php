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
            'soa_edit' => $this->data->getSoaEdit(),
            'soa_edit_api' => $this->data->getSoaEditApi(),
            'masters' => $this->data->getMasters(),
            'nameservers' => $this->data->getNameservers(),
            'nsec3param' => $this->data->getNsec3param(),
            'account' => $this->data->getAccount(),
            'rrsets' => $this->transformResourceSets(),
        ];
    }

    /**
     * Transform the zone ResourceRecords to an array formatted as rrsets.
     *
     * @return array The transformed rrsets.
     */
    private function transformResourceSets()
    {
        $rrSetTransformer = new RRSetTransformer($this->data->getResourceRecords());

        return $rrSetTransformer->transform()->rrsets;
    }
}
