<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\Item as ItemBase;

class Item extends ItemBase implements ItemDetails
{
    public function setVatCode($value)
    {
        return $this->setParameter('VatCode', $value);
    }


    public function getVatCode()
    {
        return $this->getParameter('VatCode');
    }


    public function setPaymentMode($value)
    {
        return $this->setParameter('PaymentMode', $value);
    }


    public function getPaymentMode()
    {
        return $this->getParameter('PaymentMode');
    }


    public function setPaymentSubject($value)
    {
        return $this->setParameter('PaymentSubject', $value);
    }


    public function getPaymentSubject()
    {
        return $this->getParameter('PaymentSubject');
    }
}
