<?php

namespace Omnipay\YooKassa\Trait;


trait LocaleParametersTrait
{

    public function getLocale()
    {
        return $this->getParameter('locale');
    }


    public function setLocale($value)
    {
        return $this->setParameter('locale', $value);
    }

}