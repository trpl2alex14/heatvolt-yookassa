<?php

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\YooKassa\Trait\AuthParametersTrait;
use RuntimeException;
use Throwable;
use YooKassa\Client;


abstract class Request extends AbstractRequest
{
    use AuthParametersTrait;

    protected string|null $method = null;

    protected string $responseClass = PaymentResponse::class;

    protected Client|null $client = null;

    protected bool $idempotencyRequest = true;

    protected bool $needId = true;


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
    public function sendData($data): AbstractResponse
    {
        if (!$this->method) {
            throw new RuntimeException('You must set call method name before accessing the Response!');
        }

        try {
            $parameters = [$data];
            if (method_exists($this, 'getPaymentId') && $this->needId) {
                $parameters[] = $this->getPaymentId();
            } elseif (method_exists($this, 'getId') && $this->needId) {
                $parameters[] = $this->getId();
            }

            if ($this->idempotencyRequest) {
                $parameters[] = $this->getIdempotencyKey() ?: $this->makeIdempotencyKey();
            }

            $parameters = array_filter($parameters);

            $paymentResponse = call_user_func(
                [$this->getClient(), $this->method],
                ...$parameters
            );

            return $this->response = new $this->responseClass($this, $paymentResponse);
        } catch (Throwable $e) {
            throw new InvalidRequestException('Failed to request: ' . $e->getMessage(), 0, $e);
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