<?php

namespace Exonet\Powerdns\Transformers;

class CommentTransformer extends Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform()
    {
        return (object) [
            'content' => $this->data->getContent(),
            'account' => $this->data->getAccount(),
            'modified_at' => $this->data->getModifiedAt(),
        ];
    }
}
