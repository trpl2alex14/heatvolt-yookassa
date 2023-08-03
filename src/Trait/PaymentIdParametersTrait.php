<?php

namespace Omnipay\YooKassa\Trait;


trait PaymentIdParametersTrait
{
    public function setPaymentId($value)
    {
        return $this->setParameter('paymentId', $value);
    }


    public function getPaymentId()
    {
        return $this->getParameter('paymentId');
    }
}