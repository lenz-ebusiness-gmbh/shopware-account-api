<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Tests;

use Lenz\ShopwareAccountApi\Auth\KratosAuthenticator;
use Lenz\ShopwareAccountApi\Credentials;
use Lenz\ShopwareAccountApi\Enum\OrderSequence;
use Lenz\ShopwareAccountApi\Enum\PluginGeneration;
use Lenz\ShopwareAccountApi\Enum\SalesVariantType;
use Lenz\ShopwareAccountApi\Enum\TicketStatus;
use Lenz\ShopwareAccountApi\Exception\ApiException;
use Lenz\ShopwareAccountApi\Http\HttpTransport;
use Lenz\ShopwareAccountApi\Request\Plugin\PluginListRequest;
use Lenz\ShopwareAccountApi\Request\Plugin\PluginUsageRequest;
use Lenz\ShopwareAccountApi\Request\Sales\SalesRequest;
use Lenz\ShopwareAccountApi\Request\Support\SupportTicketListRequest;
use Lenz\ShopwareAccountApi\ShopwareAccountClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ShopwareAccountClientTest extends TestCase
{
    private Psr17Factory $factory;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
    }

    public function testGetTokenAuthenticatesOnceAndCachesToken(): void
    {
        $mock = new MockClient($this->json(['token' => 'jwt-123']));
        $client = $this->clientWith($mock);

        self::assertSame('jwt-123', $client->getToken());
        self::assertCount(1, $mock->requests, 'default auth is a single POST /accesstokens');
        self::assertStringContainsString('/accesstokens', (string) $mock->requests[0]->getUri());

        self::assertSame('jwt-123', $client->getToken());
        self::assertCount(1, $mock->requests, 'cached token must not re-authenticate');
    }

    public function testSendSupportTicketListBuildsUrlAndSendsAuthHeader(): void
    {
        $mock = $this->authThen($this->json([['id' => 331165, 'subject' => 'USTID']]));
        $client = $this->clientWith($mock);

        $request = (new SupportTicketListRequest(245))->limit(5)->status('open');
        $tickets = $client->send($request);

        self::assertSame([['id' => 331165, 'subject' => 'USTID']], $tickets);

        $uri = (string) $mock->lastRequest()->getUri();
        self::assertStringContainsString('/producers/245/supporttickets', $uri);
        self::assertStringContainsString('limit=5', $uri);
        self::assertStringContainsString('status=open', $uri);
        self::assertStringContainsString('orderBy=lastContact', $uri);
        self::assertSame('jwt-123', $mock->lastRequest()->getHeaderLine('X-Shopware-Token'));
    }

    public function testPluginListSerialisesFiltersAndSimpleData(): void
    {
        $mock = $this->authThen($this->json([]));
        $client = $this->clientWith($mock);

        $request = (new PluginListRequest(245))
            ->where('isCompatible', false)
            ->where('shopwareVersion', 5)
            ->simpleData();
        $client->send($request);

        $query = urldecode((string) $mock->lastRequest()->getUri());
        self::assertStringContainsString('producerId=245', $query);
        self::assertStringContainsString('simpleData=true', $query);
        self::assertStringContainsString('"property":"isCompatible","value":false', $query);
        self::assertStringContainsString('"property":"shopwareVersion","value":5', $query);
    }

    public function testBooleanQueryParamsBecomeTrueFalseStrings(): void
    {
        $mock = $this->authThen($this->json([]));
        $client = $this->clientWith($mock);

        $client->send((new SalesRequest(245))->variantType('buy')->onlyWithSubscriptions());

        $uri = (string) $mock->lastRequest()->getUri();
        self::assertStringContainsString('variantType=buy', $uri);
        self::assertStringContainsString('onlyWithSubscriptions=true', $uri);
    }

    public function testEnumValuesAreSerialisedToTheirBackingValue(): void
    {
        $mock = $this->authThen($this->json([]));
        $client = $this->clientWith($mock);

        $client->send(
            (new SupportTicketListRequest(245))
                ->status(TicketStatus::Answered)
                ->orderBy('lastContact', OrderSequence::Asc),
        );
        self::assertStringContainsString('status=answered', (string) $mock->lastRequest()->getUri());
        self::assertStringContainsString('orderSequence=asc', (string) $mock->lastRequest()->getUri());

        $mock2 = $this->authThen($this->json([]));
        $client2 = $this->clientWith($mock2);
        $client2->send((new SalesRequest(245))->variantType(SalesVariantType::Rent));
        self::assertStringContainsString('variantType=rent', (string) $mock2->lastRequest()->getUri());

        $mock3 = $this->authThen($this->json([]));
        $client3 = $this->clientWith($mock3);
        $client3->send((new PluginListRequest(245))->where('generation', PluginGeneration::Platform));
        self::assertStringContainsString('"property":"generation","value":"platform"', urldecode((string) $mock3->lastRequest()->getUri()));
    }

    public function testPaginateIteratesAllPages(): void
    {
        $mock = $this->authThen(
            $this->json([['id' => 1], ['id' => 2]]), // full page (size 2) -> continue
            $this->json([['id' => 3]]),              // short page -> stop
        );
        $client = $this->clientWith($mock);

        $items = iterator_to_array($client->paginate(new PluginListRequest(245), 2), false);

        self::assertCount(3, $items);
        self::assertSame(['id' => 3], $items[2]);
    }

    public function testKratosAuthenticatorCanBeUsedAsAlternative(): void
    {
        $mock = new MockClient(
            $this->json(['ui' => ['action' => 'https://auth-api.shopware.com/x']]),
            $this->json(['session_token' => 'sess-1']),
            $this->json(['tokenized' => 'jwt-xyz']),
            $this->json([['id' => 1]]),
        );
        $transport = new HttpTransport($mock, $this->factory, $this->factory);
        $client = new ShopwareAccountClient(
            new Credentials('user@example.com', 'secret'),
            null,
            $mock,
            $this->factory,
            $this->factory,
            50,
            new KratosAuthenticator($transport),
        );

        $client->send(new SupportTicketListRequest(245));

        self::assertCount(4, $mock->requests, '3 auth steps + 1 API call');
        self::assertSame('jwt-xyz', $mock->lastRequest()->getHeaderLine('X-Shopware-Token'));
    }

    public function testNon2xxResponseRaisesApiExceptionWithDetail(): void
    {
        $mock = $this->authThen($this->json(['success' => false, 'code' => 'NotFound', 'detail' => 'Plugin missing'], 404));
        $client = $this->clientWith($mock);

        try {
            $client->send(new PluginUsageRequest(19561));
            self::fail('Expected ApiException');
        } catch (ApiException $e) {
            self::assertSame(404, $e->statusCode);
            self::assertStringContainsString('code NotFound', $e->getMessage());
            self::assertStringContainsString('Plugin missing', $e->getMessage());
        }
    }

    private function authThen(ResponseInterface ...$responses): MockClient
    {
        return new MockClient($this->json(['token' => 'jwt-123']), ...$responses);
    }

    private function clientWith(MockClient $mock): ShopwareAccountClient
    {
        return new ShopwareAccountClient(
            new Credentials('user@example.com', 'secret'),
            null,
            $mock,
            $this->factory,
            $this->factory,
        );
    }

    /**
     * @param array<int|string, mixed> $data
     */
    private function json(array $data, int $status = 200): ResponseInterface
    {
        $body = $this->factory->createStream(json_encode($data, JSON_THROW_ON_ERROR));

        return $this->factory->createResponse($status)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }
}
