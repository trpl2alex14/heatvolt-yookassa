<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\Trait\CaptureParametersTrait;
use Omnipay\YooKassa\Trait\LocaleParametersTrait;


class PurchaseRequest extends Request
{
    use LocaleParametersTrait, CaptureParametersTrait;

    protected ?string $method = 'createPayment';


    public function setVatCode($value)
    {
        return $this->setParameter('vatCode', $value);
    }


    public function getVatCode()
    {
        return $this->getParameter('vatCode');
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

    /**
     * @throws InvalidRequestException
     */
    public function getItems(): array
    {
        return array_map(function (Item $item) {
            $item->validate('description', 'quantity', 'price');

            if(!$item->getVatCode() && !$this->getVatCode()){
                throw new InvalidRequestException("The VatCode parameter is required");
            }

            return [
                'description' => $item->getDescription(),
                'quantity' => $item->getQuantity(),
                'amount' => [
                    'value' => round($item->getPrice(), 2),
                    'currency' => $this->getCurrency(),
                ],
                'vat_code' => $item->getVatCode() ?: $this->getVatCode(),
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
        $this->validate('amount', 'currency', 'returnUrl', 'description');

        $data = [
            'amount' => [
                'value' => $this->getAmount(),
                'currency' => $this->getCurrency(),
            ],
            'description' => $this->getDescription(),
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $this->getReturnUrl(),
            ],
            'capture' => $this->getCapture(),
        ];

        if($this->getParameter('locale')){
            $data['confirmation']['locale'] = $this->getLocale();
        }

        if($this->getParameter('transactionId')){
            $data['metadata'] = [
                'transactionId' => $this->getTransactionId(),
            ];
        }

        if($this->getParameter('customer') && $this->getParameter('items')){
            $data['receipt'] = [
                'customer' => $this->getCustomer(),
                'items' => $this->getItems(),
            ];
        }

        return $data;
    }
}