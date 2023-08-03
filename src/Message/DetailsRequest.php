<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Trait\PaymentIdParametersTrait;


class DetailsRequest extends Request
{
    use PaymentIdParametersTrait;

    protected string|null $method = 'getPaymentInfo';

    protected bool $idempotencyRequest = false;

    /**
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('paymentId');

        return null;
    }
}