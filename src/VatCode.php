<?php

namespace Omnipay\YooKassa;

use YooKassa\Common\AbstractEnum;


class VatCode  extends AbstractEnum
{
    const NOT_VAT = 1;

    const VAT_0 = 2;

    const VAT_10 = 3;

    const VAT_20 = 4;

    const VAT_10_110 = 5;

    const VAT_20_120 = 6;

    protected static $validValues = array(
        self::NOT_VAT => true,
        self::VAT_0 => true,
        self::VAT_10 => true,
        self::VAT_20 => true,
        self::VAT_10_110 => true,
        self::VAT_20_120 => true,
    );
}
