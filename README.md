# lenz/shopware-account-api

Standalone PHP client for the **Shopware Account / Store (SBP) API**
(`api.shopware.com`). Handles the Shopware login flow transparently and exposes
the producer-facing endpoints as **request objects**: plugins, support tickets,
sales, revenues, in-app features, statistics, store statics and more.

- Framework-agnostic — built on **PSR-18 / PSR-17** (Guzzle, Symfony HttpClient,
  … auto-discovered).
- Transparent authentication with pluggable **token caching**.
- One **request object per endpoint**; options are configured with fluent
  setters instead of long method signatures.

## Installation

```bash
composer require lenz/shopware-account-api
# plus a PSR-18 client if your project doesn't ship one:
composer require guzzlehttp/guzzle
```

## Usage

```php
use Lenz\ShopwareAccountApi\Credentials;
use Lenz\ShopwareAccountApi\ShopwareAccountClient;
use Lenz\ShopwareAccountApi\Request\Plugin\PluginListRequest;
use Lenz\ShopwareAccountApi\Request\Support\SupportTicketListRequest;
use Lenz\ShopwareAccountApi\Request\Support\SupportTicketDetailRequest;

$client = new ShopwareAccountClient(
    new Credentials('your-shopware-id@example.com', 'your-password'),
);

// Configure the request on its own object, then execute it:
$plugins = $client->send(
    (new PluginListRequest(producerId: 245))
        ->limit(100)
        ->orderBy('creationDate', 'desc')
        ->where('generation', 'platform')      // filter=[{"property":"generation","value":"platform"}]
);

$openTickets = $client->send(
    (new SupportTicketListRequest(245))->limit(5)->status('open')
);

$ticket = $client->send(new SupportTicketDetailRequest(245, 331165));
```

`send()` returns the decoded JSON body (associative array). Use `sendRaw()` when
you need response headers, e.g. the `sw-meta-total` paging total:

```php
use Lenz\ShopwareAccountApi\Request\Revenue\ExtensionPartnerRevenueListRequest;

$response = $client->sendRaw(new ExtensionPartnerRevenueListRequest(245));
$total    = (int) ($response->header('sw-meta-total') ?? -1);
$rows     = $response->json();
```

For list endpoints, `paginate()` iterates every page lazily (managing
limit/offset for you):

```php
foreach ($client->paginate(new PluginListRequest(245)) as $plugin) {
    // ... handle each plugin across all pages
}
```

> **Design note:** account context such as `producerId` and `companyId` are *not*
> credentials. They live on the individual request objects, so each request only
> asks for the ids it actually needs.

## Filters

Endpoints that accept the Shopware `filter` parameter expose a fluent `where()`:

```php
use Lenz\ShopwareAccountApi\Request\Plugin\PluginListRequest;
use Lenz\ShopwareAccountApi\Request\Statics\SoftwareVersionsRequest;

$client->send(
    (new PluginListRequest(245))
        ->where('isCompatible', false)
        ->where('shopwareVersion', 5)          // values may be string|int|bool|array
);

$client->send((new SoftwareVersionsRequest())->where('pluginGeneration', 'platform'));
```

## Enums

Closed-set parameters are typed as backed enums under
`Lenz\ShopwareAccountApi\Enum`; setters accept the enum **or** a raw string, so
values not yet enumerated still work.

| Enum | Used by |
|------|---------|
| `OrderSequence` (`asc`/`desc`) | `orderBy($field, OrderSequence::Asc)` on every list request |
| `TicketStatus` (`open`/`answered`/`closed`) | `SupportTicketListRequest::status()` |
| `SalesVariantType` (`buy`/`rent`/`free`/`test`/`support`/`abuses`/`producerLicensed`) | `SalesRequest::variantType()` |
| `PluginGeneration` (`classic`/`platform`/`apps`) | `->where('generation', PluginGeneration::Platform)` |
| `AbuseStatus` (`open`/`first_reminder`/…/`FUPAbuse`) | `PluginAbuseListRequest::status()` |

```php
use Lenz\ShopwareAccountApi\Enum\TicketStatus;

$client->send((new SupportTicketListRequest(245))->status(TicketStatus::Open));
$client->send((new SupportTicketListRequest(245))->status('open')); // still works
```

## Authentication

By default the client authenticates via `POST /accesstokens`
(`AccessTokenAuthenticator`). The token expiry is read from the JWT `exp` claim
when present, otherwise a fallback lifetime (`tokenTtlMinutes`, default 50) is used.

To use the Ory Kratos login flow (`auth-api.shopware.com`) instead, pass a
`KratosAuthenticator`:

```php
use Lenz\ShopwareAccountApi\Auth\KratosAuthenticator;
use Lenz\ShopwareAccountApi\Http\HttpTransport;

// any object implementing AuthenticatorInterface works
$client = new ShopwareAccountClient(
    new Credentials($id, $password),
    authenticator: new KratosAuthenticator(
        new HttpTransport($psr18Client, $requestFactory, $streamFactory)
    ),
);
```

## Token caching

By default the token lives in memory for the process. Persist it to avoid
re-authenticating on every run:

```php
use Lenz\ShopwareAccountApi\Token\FileTokenStorage;

$client = new ShopwareAccountClient(
    new Credentials($id, $password),
    new FileTokenStorage(__DIR__ . '/var/shopware-token.json'),
);
```

Implement `TokenStorageInterface` for your own backend (Doctrine, Redis,
PSR-6/PSR-16, …).

## Available request objects

Grouped under `Lenz\ShopwareAccountApi\Request\{Plugin,Support,InAppFeature,Sales,Revenue,Statics,Partner}`.
List requests share `limit() / offset() / orderBy() / search()`.

### Plugins
| Class | Endpoint |
|-------|----------|
| `PluginListRequest($producerId)` | `GET /plugins` (filters, `simpleData()`) |
| `PluginDetailRequest($pluginId)` | `GET /plugins/{id}` |
| `PluginPicturesRequest($pluginId)` | `GET /plugins/{id}/pictures` |
| `PluginReviewsRequest($pluginId)` | `GET /plugins/{id}/reviews` |
| `PluginReleaseRequestsRequest($pluginId)` | `GET /plugins/{id}/releaserequests` |
| `PluginLicenseCountRequest($pluginId)` | `GET /plugins/{id}/priceadjustment/licensecount` |
| `PluginTestingInstancesRequest($pluginId)` | `GET /plugins/{id}/testinginstances` |
| `PluginTestingInstancesConfigRequest()` | `GET /plugins/testinginstances/config` |
| `PluginPreviewRequest($pluginId)` | `GET /plugins/{id}/preview` |
| `PluginBinariesRequest($producerId, $pluginId)` | `GET /producers/{p}/plugins/{id}/binaries` |
| `PluginUsageRequest($pluginId)` | `GET /statistics/shopwareversiondistribution/{id}` |

### Reviews & abuses
| Class | Endpoint |
|-------|----------|
| `PluginCommentsRequest($pluginId)` | `GET /plugins/{id}/comments` |
| `PluginCommentListRequest($producerId)` | `GET /plugincomments` |
| `PluginCommentDetailRequest($commentId)` | `GET /plugincomments/{id}` |
| `PluginAbuseListRequest($producerId)` | `GET /pluginAbuses` (`status(...)`) |

### Support tickets
| Class | Endpoint |
|-------|----------|
| `SupportTicketListRequest($producerId)` | `GET /producers/{p}/supporttickets` (`status()`) |
| `SupportTicketDetailRequest($producerId, $ticketId)` | `GET /producers/{p}/supporttickets/{id}` |
| `TicketAttachmentMetaRequest($companyId, $ticketId, $attId)` | `GET /companies/{c}/supporttickets/{t}/attachments/{a}` |

### In-app features
| Class | Endpoint |
|-------|----------|
| `InAppFeatureListRequest($producerId)` | `GET /producers/{p}/inappfeatures` |
| `InAppFeatureDetailRequest($producerId, $featureId)` | `GET /producers/{p}/inappfeatures/{id}` |
| `InAppFeatureGroupListRequest($producerId)` | `GET /producers/{p}/inappfeaturegroups` |
| `InAppFeatureGroupDetailRequest($producerId, $groupId)` | `GET /producers/{p}/inappfeaturegroups/{id}` |
| `InAppFeatureGroupFeaturesRequest($producerId, $groupId)` | `GET …/inappfeaturegroups/{id}/features` |
| `InAppFeatureGroupAccessGrantsRequest($producerId, $groupId)` | `GET …/inappfeaturegroups/{id}/accessgrants` |

### Sales, revenues & payouts
| Class | Endpoint |
|-------|----------|
| `SalesRequest($producerId)` | `GET /producers/{p}/sales` (`variantType()`, flags) |
| `SalesPriceAdjustmentListRequest($producerId)` | `GET /producers/{p}/sales/priceadjustments` |
| `SalesInAppLicenseListRequest($producerId)` | `GET /producers/{p}/sales/inapplicenses` |
| `PluginWithdrawalRequestListRequest($producerId)` | `GET /producers/{p}/pluginwithdrawalrequests` |
| `ExtensionPartnerRevenueListRequest($producerId)` | `GET /producers/{p}/extensionpartnerrevenues` |
| `ExtensionPartnerRevenueBalanceRequest($producerId)` | `GET …/extensionpartnerrevenuebalance` (`disbursalStatus()`) |
| `ExtensionPartnerRevenueTaxInfoRequest($producerId)` | `GET …/extensionpartnerrevenues/taxinformation` |
| `KickbackBankDataRequest($companyId)` | `GET /companies/{c}/kickbackbankdata` |
| `PartnerPayoutListRequest($companyId)` | `GET /companies/{c}/partnerpayouts` (`context()`) |
| `PartnerDetailRequest($partnerId)` | `GET /partners/{id}` |

### Store statics & reference data
| Class | Endpoint |
|-------|----------|
| `SoftwareVersionsRequest()` | `GET /pluginstatics/softwareVersions` (filters) |
| `PluginStaticsAllRequest()` | `GET /pluginstatics/all` (filters) |
| `LocalesRequest()` | `GET /pluginstatics/locales` |
| `StoreAvailabilitiesRequest()` | `GET /pluginstatics/storeAvailabilities` |
| `ProducerSupportLanguagesRequest()` | `GET /producersupportlanguages` |
| `ProducerContractCurrentRequest()` | `GET /documents/producerContract/versions/current` |
| `WikiRequest($path, $locale = 'de')` | `GET /wiki/account/locale/{locale}/path/{path}` |

Plus on the client itself: `getToken()`, `invalidateToken()` and
`downloadAttachment($signedUrl)`.

## Errors

All calls throw `Lenz\ShopwareAccountApi\Exception\ApiException` on non-2xx
responses (with `$statusCode` / `$responseBody`) and `AuthenticationException`
when login fails. Authenticated requests re-authenticate and retry once on `401`.

## Custom requests

Any endpoint not covered yet can be added without touching the client — just
implement `Lenz\ShopwareAccountApi\Request\AccountRequest` (or extend
`AbstractRequest` / `AbstractListRequest`) and pass it to `send()`.

## Development

```bash
composer install
composer test
```

## License

MIT © LENZ eBusiness GmbH
