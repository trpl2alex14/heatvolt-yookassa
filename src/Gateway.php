<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\YooKassa\Message\CancelRequest;
use Omnipay\YooKassa\Message\CaptureRequest;
use Omnipay\YooKassa\Message\DetailsRequest;
use Omnipay\YooKassa\Message\PurchaseRequest;
use Omnipay\YooKassa\Message\ReceiptDetailsRequest;
use Omnipay\YooKassa\Message\ReceiptsRequest;
use Omnipay\YooKassa\Message\RefundRequest;
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


    public function supportsCancel()
    {
        return method_exists($this, 'cancel');
    }


    public function cancel(array $options = []): RequestInterface
    {
        return $this->createRequest(CancelRequest::class, $options);
    }


    public function capture(array $options = []): RequestInterface
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }


    public function fetchTransaction(array $options = []): RequestInterface
    {
        return $this->createRequest(DetailsRequest::class, $options);
    }


    public function refund(array $options = []): RequestInterface
    {
        return $this->createRequest(RefundRequest::class, $options);
    }


    public function supportsFetchReceipt()
    {
        return method_exists($this, 'fetchReceipt');
    }


    public function fetchReceipt(array $options = []): RequestInterface
    {
        return $this->createRequest(ReceiptDetailsRequest::class, $options);
    }


    public function supportsFetchReceipts()
    {
        return method_exists($this, 'fetchReceipts');
    }


    public function fetchReceipts(array $options = []): RequestInterface
    {
        return $this->createRequest(ReceiptsRequest::class, $options);
    }
}