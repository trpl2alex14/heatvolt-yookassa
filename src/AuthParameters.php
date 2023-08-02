<?php

namespace Omnipay\YooKassa;


trait AuthParameters
{
    public function getShopId()
    {
        return $this->traitGetParameter('shopId');
    }

    public function setShopId($value)
    {
        return $this->traitSetParameter('shopId', $value);
    }

    public function getSecret()
    {
        return $this->traitGetParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->traitSetParameter('secret', $value);
    }
}