<?php

namespace Omnipay\YooKassa\Tests\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\Message\PurchaseRequest;
use Omnipay\YooKassa\Message\PurchaseResponse;
use Omnipay\YooKassa\Message\Request;
use Omnipay\YooKassa\VatCode;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionObject;
use TypeError;
use YooKassa\Client;
use YooKassa\Client\CurlClient;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Locale;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;


class PurchaseRequestTest extends TestCase
{
    private Request $request;

    private string $shopId = '1';
    private string $secretKey = 'test_1';

    private string $currency = CurrencyCode::RUB;
    private int $vatCode = VatCode::NOT_VAT;

    private string $returnUrl = 'http://heatvolt.loc/success';
    private string $confirmUrl = 'https://yoomoney.ru/payments/external/confirmation?orderId=22e12f66-000f-5000-8000-18db351245c7';

    private array $customer = [
        [
            'phone' => '79630001122',
        ],
        [
            'email' => 'test@test.ru',
            'full_name' => 'Ivan'
        ]
    ];

    private array $items = [
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

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }


    public function provider(): array
    {
        return [
            'payment 1' => [
                [
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


    /**
     * @dataProvider provider
     * @throws InvalidRequestException
     */
    public function testGetData($parameters, $customer, $item)
    {
        $this->request->initialize($parameters);

        $data = $this->request->getData();

        $this->assertEquals($parameters['amount'], $data['amount']['value']);
        $this->assertSame($this->currency, $data['amount']['currency']);
        $this->assertSame($parameters['description'], $data['description']);
        $this->assertSame($this->returnUrl, $data['confirmation']['return_url']);
        $this->assertSame(Locale::RUSSIAN, $data['confirmation']['locale']);
        if(isset($parameters['transactionId'])) {
            $this->assertSame($parameters['transactionId'], $data['metadata']['transactionId']);
        }else{
            $this->assertFalse(isset($data['metadata']['transactionId']));
        }

        $this->assertEquals($customer, $data['receipt']['customer']);

        $this->assertEquals($item['price'], $data['receipt']['items'][0]['amount']['value']);
        $this->assertSame($item['description'], $data['receipt']['items'][0]['description']);
        $this->assertSame($item['quantity'], $data['receipt']['items'][0]['quantity']);
        $this->assertSame($item['paymentSubject'], $data['receipt']['items'][0]['payment_subject']);
        $this->assertSame($item['paymentMode'], $data['receipt']['items'][0]['payment_mode']);
        $this->assertSame($this->currency, $data['receipt']['items'][0]['amount']['currency']);
        $this->assertSame($this->vatCode, $data['receipt']['items'][0]['vat_code']);
    }

    /**
     * @dataProvider provider
     * @throws ReflectionException
     */
    public function testSendData($parameters)
    {
        $this->request->initialize($parameters);

        $curlClientStub = $this->getCurlClientStub();
        $curlClientStub->method('sendRequest')->willReturn([
            [],
            $this->fixture('payment.pending'),
            ['http_code' => 200],
        ]);

        $this->getYooKassaClient($this->request)
            ->setApiClient($curlClientStub)
            ->setAuth($this->shopId, $this->secretKey);

        $response = $this->request->send();
        $this->assertInstanceOf(PurchaseResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertTrue($response->isPending());
        $this->assertSame($response->getRedirectUrl(), $this->confirmUrl);
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
     * @dataProvider providerBadData
     * @throws InvalidRequestException
     */
    public function testBadGetData($parameters, $exception)
    {
        $this->expectException($exception);

        $this->request->initialize($parameters);

        $this->request->getData();
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