<?php

namespace Omnipay\YooKassa;


interface ItemDetails
{
    public function getVatCode();

    public function getPaymentMode();

    public function getPaymentSubject();
}