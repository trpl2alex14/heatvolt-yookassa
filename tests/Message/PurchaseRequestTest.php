<?php


namespace Omnipay\YooKassa\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\YooKassa\Customer;
use Omnipay\YooKassa\Item;
use Omnipay\YooKassa\Message\PurchaseRequest;
use Omnipay\YooKassa\Message\PurchaseResponse;
use Omnipay\YooKassa\Message\Request;
use ReflectionObject;
use YooKassa\Client;

class PurchaseRequestTest extends TestCase
{
    private $request;

    private $shopId = '1';
    private $secretKey = 'test_1';

    private $transactionId = 'sdfdsfdsf34234';
    private $amount = '155.00';
    private $currency = 'RUB';
    private $description = 'Test purchase description';
    private $returnUrl = 'http://heatvolt.loc/success';


    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'transactionId' => $this->transactionId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'returnUrl' => $this->returnUrl,
            'items' => [
                new Item([
                    'description' => 'Test item',
                    'quantity' => 5,
                    'price' => '31'
                ])
            ],
            'customer' => new Customer([
                'phone' => '79630001122'
            ])
        ]);
    }


    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['amount']['value']);
        $this->assertSame($this->currency, $data['amount']['currency']);
        $this->assertSame($this->description, $data['description']);
        $this->assertSame($this->returnUrl, $data['confirmation']['return_url']);
        $this->assertSame($this->transactionId, $data['metadata']['transactionId']);
    }


    public function testSendData()
    {
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
    }


    protected function getCurlClientStub()
    {
        return $this->getMockBuilder(Client\CurlClient::class)
            ->setMethods(['sendRequest'])
            ->getMock();
    }


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