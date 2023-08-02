<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\LocaleParametersTrait;


class PurchaseRequest extends Request
{
    use LocaleParametersTrait;

    protected string|null $method = 'createPayment';


    public function getCapture()
    {
        $capture = $this->getParameter('capture');

        return $capture === null ? true : $capture;
    }


    public function setCapture($value): self
    {
        return $this->setParameter('capture', $value);
    }

    /**
     * @throws InvalidRequestException
     */
    public function getCustomer(): array
    {
        $customer = $this->getParameter('customer');

        if(!$customer instanceof Customer){
            throw new InvalidRequestException("The customer parameter is required");
        }

        return $customer->validated();
    }


    public function setCustomer(Customer $value): self
    {
        return $this->setParameter('customer', $value);
    }


    public function getItems(): array
    {
        return array_map(function (Item $item) {
            return [
                'description' => $item->getDescription(),
                'quantity' => $item->getQuantity(),
                'amount' => [
                    'value' => round($item->getPrice(), 2),
                    'currency' => $this->getCurrency(),
                ],
                'vat_code' => $item->getVatCode(),
                'payment_mode' => $item->getPaymentMode(),
                'payment_subject' => $item->getPaymentSubject(),
            ];
        }, parent::getItems()->all());
    }


    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('amount', 'currency', 'returnUrl', 'transactionId', 'description', 'items', 'customer');

        return [
            'amount' => [
                'value' => $this->getAmount(),
                'currency' => $this->getCurrency(),
            ],
            'description' => $this->getDescription(),
            'confirmation' => [
                'type' => 'redirect',
                'locale' => $this->getLocale(),
                'return_url' => $this->getReturnUrl(),
            ],
            'capture' => $this->getCapture(),
            'metadata' => [
                'transactionId' => $this->getTransactionId(),
            ],
            'receipt' => [
                'customer' => $this->getCustomer(),
                'items' => $this->getItems(),
            ],
        ];
    }
}