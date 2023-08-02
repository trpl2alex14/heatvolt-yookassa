<?php

namespace Omnipay\YooKassa;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\YooKassa\Message\PurchaseRequest;

/**
 * @method NotificationInterface acceptNotification(array $options = array())
 * @method RequestInterface capture(array $options = array())
 */
class Gateway extends AbstractGateway
{
    use AuthParameters;

    public function getName()
    {
        return 'YooKassa';
    }

    public function getDefaultParameters()
    {
        return [
            'testMode' => false,
        ];
    }

    public function getShopId()
    {
        return $this->getParameter('shopId');
    }

    public function setShopId($value)
    {
        return $this->setParameter('shopId', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }
}