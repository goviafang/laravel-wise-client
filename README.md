# laravel-wise-client

A Laravel client for the [Wise Platform API](https://wise.com). Covers the core money-movement flow — profiles, quotes, recipients, transfers, balances, activity, exchange rates, and webhooks.

繁體中文文件請見 [README.zh-TW.md](README.zh-TW.md)。

## Before you start

This package only talks to Wise's API — it can't get you access to it. Sort out credentials on Wise's side first.

**Sandbox (for development)**
1. Sign up for a free sandbox account at [sandbox.transferwise.tech/register](https://sandbox.transferwise.tech/register) — email and password only, no verification, pre-loaded with 1,000,000 GBP test money.
2. Log in, go to **Settings → API tokens**, and generate a personal token. Sandbox 2FA codes are always `111111`.
3. Put it in `.env` as `WISE_PERSONAL_TOKEN`, with `WISE_ENVIRONMENT=sandbox`.

**Production**
1. Create a real Wise account and turn on 2-step verification — required before you can generate a token.
2. On [wise.com](https://wise.com), go to **Settings → API tokens** and generate a personal token (this isn't available from the mobile app).
3. Set `WISE_ENVIRONMENT=production` and swap in the production token.

**OAuth (`client_credentials` / `user_token` drivers)**
`client_id`/`client_secret` aren't self-service — Wise issues them after a partnership process. See the [Wise Platform API reference](https://docs.wise.com/api-reference) and the [going-live checklist](https://docs.wise.com/guides/product/kyc/partner-accounts#go-live). If you're only integrating with your own personal or business account, the personal token above is all you need — skip this.

**Webhooks**
Signatures are RSA/SHA256 over the raw body. Check the current sandbox/production public key in the [webhook event docs](https://docs.wise.com/api-reference/webhook-event) before setting `WISE_WEBHOOK_PUBLIC_KEY`.

## Install

```bash
composer require goviafang/laravel-wise-client
```

Publish the config file:

```bash
php artisan vendor:publish --tag=wise-config
```

Set the credentials that match your auth driver in `.env`:

```env
WISE_ENVIRONMENT=sandbox
WISE_AUTH_DRIVER=personal_token
WISE_PERSONAL_TOKEN=your-personal-token
```

## Quick start

```php
use Govia\WiseClient\Facades\Wise;
use Govia\WiseClient\DTO\CreateQuoteData;
use Illuminate\Support\Str;

$transfer = Wise::transferBuilder(profileId: 12345)
    ->quote(new CreateQuoteData(
        profileId: 12345,
        sourceCurrency: 'GBP',
        targetCurrency: 'USD',
        sourceAmount: 100.0,
    ))
    ->recipient($recipientId)
    ->fund()
    ->create(customerTransactionId: (string) Str::uuid());
```

Or call the resources directly without the builder — the builder is just a convenience wrapper over the same methods:

```php
use Govia\WiseClient\DTO\CreateTransferData;

$quote = Wise::quotes()->create(new CreateQuoteData(
    profileId: 12345,
    sourceCurrency: 'GBP',
    targetCurrency: 'USD',
    sourceAmount: 100.0,
));

$transfer = Wise::transfers()->create(new CreateTransferData(
    quoteId: $quote->id,
    targetRecipientId: $recipientId,
    customerTransactionId: (string) Str::uuid(),
));

Wise::transfers()->fund(profileId: 12345, transferId: $transfer->id);
```

## Documentation

- [Installation & configuration](docs/en/installation.md)
- [Authentication](docs/en/authentication.md)
- [Resources & usage](docs/en/resources.md)
- [Webhooks](docs/en/webhooks.md)
- [Error handling](docs/en/errors.md)

## Scope

This package targets the core transfer flow only. Card issuing, spend controls, disputes, and the biometric SCA flows (PIN, face scan, device fingerprint) are out of scope — see [docs/en/resources.md](docs/en/resources.md) for the full list of what's covered.

## Testing

```bash
composer test
composer stan
composer format
```

## License

MIT.
