<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\YooKassa\AuthParameters;
use Throwable;
use YooKassa\Client;


class PurchaseRequest extends AbstractRequest
{
    use AuthParameters;


    public function getData()
    {
        $this->validate('amount', 'currency', 'returnUrl', 'transactionId', 'description', 'items', 'customer');

        $data = [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'return_url' => $this->getReturnUrl(),
            'transactionId' => $this->getTransactionId(),
            'items' => $this->getItems(),
            // 'customer' => $this->getCustomer(),
        ];

        return [
            'payment' => [
                'amount' => [
                    'value' => $data['amount'],
                    'currency' => $data['currency'],
                ],
                'description' => $data['description'],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $data['return_url'],
                ],
                'metadata' => [
                    'transactionId' => $data['transactionId'],
                ],
                'receipt' => [
                    'customer' => $data['customer'],
//                    'items' => array_map(function (ItemInterface $item) {
//                        return [
//                            'description' => $item->getDescription(),
//                            'quantity' => $item->getQuantity(),
//                            'amount' => [
//                                'value' => round($item->getPrice(), 2),
//                                'currency' => 'RUB',
//                            ],
//                            'vat_code' => $item->getVatCode(),
//                            'payment_mode' => $item->getPaymentMode(),
//                            'payment_subject' => $item->getPaymentSubject(),
//                        ];
//                    }, $data['items']->all()),
                ],
            ],
            'idempotencyKey' => $this->makeIdempotencyKey($data)
        ];
    }

    /**
     * @throws InvalidRequestException
     */
    public function sendData($data)
    {
        try {
            $paymentResponse = call_user_func([$this->getClient(), 'createPayment'], $data);

            return $this->response = new PurchaseResponse($this, $paymentResponse);
        } catch (Throwable $e) {
            throw new InvalidRequestException('Failed to request purchase: ' . $e->getMessage(), 0, $e);
        }
    }


    protected function getClient(): Client
    {
        return (new Client())->setAuth($this->getShopId(), $this->getSecret());
    }


    private function makeIdempotencyKey($data): string
    {
        return md5(json_encode($data));
    }
}