<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Trait\PaymentIdParametersTrait;


class RefundRequest extends PurchaseRequest
{
    use PaymentIdParametersTrait;

    protected ?string $method = 'createRefund';

    protected string $responseClass = RefundResponse::class;

    protected bool $needPaymentId = false;


    public function getCapture()
    {
        return null;
    }


    public function setCapture($value): self
    {
        return $this;
    }

    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('amount', 'currency', 'paymentId');

        $data = [
            'payment_id' => $this->getPaymentId(),
            'amount' => [
                'value' => $this->getAmount(),
                'currency' => $this->getCurrency(),
            ],
        ];

        if ($this->getParameter('description')) {
            $data['description'] = $this->getDescription();
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