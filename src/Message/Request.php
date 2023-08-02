<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\YooKassa\AuthParametersTrait;
use RuntimeException;
use Throwable;
use YooKassa\Client;


abstract class Request extends AbstractRequest
{
    use AuthParametersTrait;

    protected string|null $method = null;

    protected Client|null $client = null;


    public function setIdempotencyKey($value): Request
    {
        return $this->setParameter('IdempotencyKey', $value);
    }


    public function getIdempotencyKey()
    {
        return $this->getParameter('IdempotencyKey');
    }

    /**
     * @throws InvalidRequestException
     */
    public function sendData($data): PurchaseResponse
    {
        if(!$this->method){
            throw new RuntimeException('You must set call method name before accessing the Response!');
        }

        try {
            $paymentResponse = call_user_func(
                [$this->getClient(), $this->method],
                $data, $this->getIdempotencyKey() ?: $this->makeIdempotencyKey()
            );

            return $this->response = new PurchaseResponse($this, $paymentResponse);
        } catch (Throwable $e) {
            throw new InvalidRequestException('Failed to request purchase: ' . $e->getMessage(), 0, $e);
        }
    }


    protected function getClient(): Client
    {
        if (is_null($this->client) || !($this->client instanceof Client)) {
            $this->client = (new Client())
                ->setAuth($this->getShopId(), $this->getSecret());
        }

        return $this->client;
    }


    private function makeIdempotencyKey(): string
    {
        $data = $this->getData();

        $key = md5(json_encode($data));

        $this->setIdempotencyKey($key);

        return $key;
    }
}