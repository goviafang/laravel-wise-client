# Installation & configuration

## Prerequisites

You need credentials from Wise before this package is useful — it's a client, not a way around Wise's own onboarding.

- **Sandbox account** — sign up free at [sandbox.transferwise.tech/register](https://sandbox.transferwise.tech/register) (email + password, no verification, pre-loaded with 1,000,000 GBP test money). 2FA codes in sandbox are always `111111`.
- **Personal token** — in either environment, go to **Settings → API tokens** on the account and generate one. Requires 2-step verification on the account; not available from the mobile app.
- **OAuth credentials** (`client_id`/`client_secret`, only needed for the `client_credentials` or `user_token` drivers) — issued by Wise after a partnership process, not self-service. See the [API reference](https://docs.wise.com/api-reference) and the [going-live checklist](https://docs.wise.com/guides/product/kyc/partner-accounts#go-live). Skip this if you're only integrating with your own account.
- **Webhook public key** (only if you use webhooks) — check the current sandbox/production key in the [webhook event docs](https://docs.wise.com/api-reference/webhook-event).

Full details in the [Authentication](authentication.md) page.

## Requirements

- PHP 8.2+
- Laravel 12

## Install

```bash
composer require goviafang/laravel-wise-client
```

The service provider and `Wise` facade are auto-discovered. Publish the config file if you want to edit it directly instead of relying on environment variables:

```bash
php artisan vendor:publish --tag=wise-config
```

This copies `config/wise.php` into your app.

## Environment

`WISE_ENVIRONMENT` picks which Wise server the package talks to:

- `sandbox` (default) → `https://api.wise-sandbox.com`
- `production` → `https://api.wise.com`

Both are combined with `WISE_API_VERSION` (default `2026Q3`) to build the base URL. Wise rolls this version forward every quarter — when they do, bump the env var rather than waiting for a package update:

```env
WISE_ENVIRONMENT=sandbox
WISE_API_VERSION=2026Q3
```

## HTTP behaviour

```env
WISE_HTTP_TIMEOUT=30
WISE_RETRY_ON_RATE_LIMIT=false
WISE_RETRY_MAX_ATTEMPTS=3
```

Wise responds to rate limiting with a `429` and a `Retry-After` header, nothing more. Automatic retries are off by default because a silent retry on a write endpoint like transfer creation can be riskier than just letting the exception surface. Turn `WISE_RETRY_ON_RATE_LIMIT` on if you've decided the risk is acceptable for your use case (typically fine for read-only endpoints).

See [Authentication](authentication.md) for the auth-specific env variables.
