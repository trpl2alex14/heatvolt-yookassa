<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\YooKassa\Message\PurchaseRequest;


class Gateway extends AbstractGateway
{
    use AuthParametersTrait, LocaleParametersTrait;

    public function getName()
    {
        return 'YooKassa';
    }

    public function getDefaultParameters():array
    {
        return [
            'locale' => 'ru_RU',
            'testMode' => false,
        ];
    }

    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }
}