<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Trait\PaymentIdParametersTrait;


class CaptureRequest extends PurchaseRequest
{
    use PaymentIdParametersTrait;

    protected string|null $method = 'capturePayment';

    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('amount', 'paymentId');

        $data = [
            'amount' => [
                'value' => $this->getAmount(),
            ],
        ];

        if ($this->getParameter('currency')) {
            $data['amount']['currency'] = $this->getCurrency();
        }

        if ($this->getParameter('customer') && $this->getParameter('items')) {
            $data['receipt'] = [
                'customer' => $this->getCustomer(),
                'items' => $this->getItems(),
            ];
        }

        return $data;
    }
}