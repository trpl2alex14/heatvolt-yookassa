<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\Item as ItemBase;

class Item extends ItemBase implements ItemDetails
{


    public function setVatCode($value)
    {
        return $this->setParameter('vatCode', $value);
    }


    public function getVatCode()
    {
        return $this->getParameter('vatCode');
    }


    public function setPaymentMode($value)
    {
        return $this->setParameter('paymentMode', $value);
    }


    public function getPaymentMode()
    {
        return $this->getParameter('paymentMode');
    }


    public function setPaymentSubject($value)
    {
        return $this->setParameter('paymentSubject', $value);
    }


    public function getPaymentSubject()
    {
        return $this->getParameter('paymentSubject');
    }
}
