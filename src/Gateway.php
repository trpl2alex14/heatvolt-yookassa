<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\YooKassa\Message\PurchaseRequest;
use Omnipay\YooKassa\Trait\AuthParametersTrait;
use Omnipay\YooKassa\Trait\CaptureParametersTrait;
use Omnipay\YooKassa\Trait\LocaleParametersTrait;


class Gateway extends AbstractGateway
{
    use AuthParametersTrait, LocaleParametersTrait, CaptureParametersTrait;

    public function getName()
    {
        return 'YooKassa';
    }


    public function getDefaultParameters():array
    {
        return [
            'locale' => 'ru_RU',
            'testMode' => false,
            'capture' => true,
        ];
    }


    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }
}