<?php

namespace Omnipay\YooKassa\Trait;


trait CaptureParametersTrait
{
    public function getCapture()
    {
        $capture = $this->getParameter('capture');

        return $capture === null ? true : $capture;
    }


    public function setCapture($value): self
    {
        return $this->setParameter('capture', $value);
    }

}