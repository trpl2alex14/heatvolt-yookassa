<?php

namespace Omnipay\YooKassa\Tests\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\Message\Request;
use Omnipay\YooKassa\VatCode;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionObject;
use TypeError;
use YooKassa\Client;
use YooKassa\Client\CurlClient;
use YooKassa\Model\CurrencyCode;
use YooKassa\Request\Payments\Locale;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;


abstract class AbstractRequestTest extends TestCase
{
    protected Request $request;

    protected string $requestName;

    protected string $shopId = '239572';
    protected string $secretKey = 'test_CE99VzHivJoYF8Ia-T0YS3VoJgkWryXIXqBcqQY7dk4';

    protected string $currency = CurrencyCode::RUB;
    protected int $vatCode = VatCode::NOT_VAT;

    protected string $returnUrl = 'http://heatvolt.loc/success';
    protected string $confirmUrl = 'https://yoomoney.ru/payments/external/confirmation?orderId=22e12f66-000f-5000-8000-18db351245c7';

    protected array $customer = [
        [
            'phone' => '79630001122',
        ],
        [
            'email' => 'test@test.ru',
            'full_name' => 'Ivan'
        ]
    ];

    protected array $items = [
        [
            'description' => 'Test item',
            'quantity' => 4,
            'price' => '31',
            'paymentSubject' => PaymentSubject::COMMODITY,
            'paymentMode' => PaymentMode::FULL_PAYMENT,
        ],
        [
            'description' => 'Test item 2',
            'quantity' => 1,
            'price' => '31',
            'paymentSubject' => PaymentSubject::COMMODITY,
            'paymentMode' => PaymentMode::FULL_PAYMENT,
        ]
    ];


    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new $this->requestName($this->getHttpClient(), $this->getHttpRequest());
    }


    public function provider(): array
    {
        return [
            'payment 1' => [
                [
                    'shopId' => $this->shopId,
                    'secret' => $this->secretKey,
                    'locale' => Locale::RUSSIAN,
                    'transactionId' => 'sdfdsfdsf34234',
                    'amount' => 155,
                    'currency' => $this->currency,
                    'description' => 'Test purchase description 1',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'items' => array_map(fn($item) => new Item($item), $this->items),
                    'customer' => new Customer($this->customer[0])
                ],
                $this->customer[0],
                $this->items[0]
            ],
            'payment 2' => [
                [
                    'shopId' => $this->shopId,
                    'secret' => $this->secretKey,
                    'locale' => Locale::RUSSIAN,
                    'amount' => 31,
                    'currency' => $this->currency,
                    'description' => 'Order 123123',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'items' => array_map(fn($item) => new Item($item), [$this->items[1]]),
                    'customer' => new Customer($this->customer[1])
                ],
                $this->customer[1],
                $this->items[1]
            ],
        ];
    }


    public function providerBadData(): array
    {
        return [
            'required amount' => [
                [
                    'currency' => $this->currency,
                    'description' => 'Test purchase description 1',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'items' => array_map(fn($item) => new Item($item), $this->items),
                    'customer' => new Customer($this->customer[0])
                ],
                InvalidRequestException::class
            ],
            'customer type' => [
                [
                    'amount' => 31,
                    'currency' => $this->currency,
                    'description' => 'Order 123123',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'customer' => ['phone' => '123123123'],
                    'items' => array_map(fn($item) => new Item($item), $this->items)
                ],
                TypeError::class
            ],
            'customer field phone' => [
                [
                    'amount' => 31,
                    'currency' => $this->currency,
                    'description' => 'Order 123123',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'customer' => new Customer(['phone' => '7963001131a']),
                    'items' => array_map(fn($item) => new Item($item), $this->items)
                ],
                InvalidRequestException::class
            ],
            'customer required fields' => [
                [
                    'amount' => 31,
                    'currency' => $this->currency,
                    'description' => 'Order 123123',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'customer' => new Customer(['email' => 'adasda@adsad.ru']),
                    'items' => array_map(fn($item) => new Item($item), $this->items)
                ],
                InvalidRequestException::class
            ],
            'items required vat code' => [
                [
                    'currency' => $this->currency,
                    'description' => 'Test purchase description 1',
                    'returnUrl' => $this->returnUrl,
                    'items' => array_map(fn($item) => new Item($item), [
                        [
                            'description' => 'item 1',
                            'quantity' => 1,
                            'price' => 150
                        ]
                    ]),
                    'customer' => new Customer($this->customer[0])
                ],
                InvalidRequestException::class
            ],
            'items required fields' => [
                [
                    'currency' => $this->currency,
                    'description' => 'Test purchase description 1',
                    'returnUrl' => $this->returnUrl,
                    'vatCode' => $this->vatCode,
                    'items' => array_map(fn($item) => new Item($item), [
                        [
                            'description' => 'item 1',
                            'quantity' => 1,
                        ]
                    ]),
                    'customer' => new Customer($this->customer[0])
                ],
                InvalidRequestException::class
            ],
        ];
    }


    /**
     * @throws ReflectionException
     */
    protected function sendMockRequest($request, $fixture)
    {
        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub->method('sendRequest')->willReturn([
            [],
            $this->fixture($fixture),
            ['http_code' => 200],
        ]);

        $this->getYooKassaClient($request)
            ->setApiClient($curlClientStub)
            ->setAuth($this->shopId, $this->secretKey);

        return $this->request->send();
    }

    /**
     * @return MockObject|CurlClient
     */
    protected function getCurlClientStub(): CurlClient|MockObject
    {
        return $this->getMockBuilder(CurlClient::class)
            ->setMethods(['sendRequest'])
            ->getMock();
    }

    /**
     * @throws ReflectionException
     */
    protected function getYooKassaClient(Request $request): Client
    {
        $clientMethod = (new ReflectionObject($request))->getMethod('getClient');
        $clientMethod->setAccessible(true);

        return $clientMethod->invoke($request);
    }


    protected function fixture(string $name): string
    {
        return file_get_contents(__DIR__ . '/fixture/' . $name . '.json');
    }
}