# Webhooks

## Subscribing

```php
Wise::webhooks()->subscribe($profileId, new CreateWebhookSubscriptionData(
    name: 'Transfer updates',
    triggerOn: 'transfers#state-change',
    deliveryUrl: 'https://your-app.test/wise/webhook',
));

Wise::webhooks()->list($profileId);
Wise::webhooks()->unsubscribe($profileId, $subscriptionId);
```

Wise publishes the full list of event types (`transfers#state-change`, `balances#update`, `profiles#state-change`, and so on) in their own API reference — this package doesn't hardcode them, since the mapping between `triggerOn` and what your app does with it belongs in your app, not the client library.

## Receiving

Every webhook call is signed with `X-Signature-SHA256`, an RSA-SHA256 signature over the raw body, verified against the public key Wise gives you when you set up the subscription. Put it in `.env`:

```env
WISE_WEBHOOK_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----"
```

Register a route with the bundled middleware:

```php
Route::post('/wise/webhook', WiseWebhookController::class)
    ->middleware('wise.verify-signature');
```

The middleware rejects the request with a `401` if the signature doesn't check out, and — once it does — dispatches a `Govia\WiseClient\Webhooks\Events\WiseWebhookReceived` Laravel event with the parsed payload. Your controller doesn't need to do anything but return a `200`:

```php
class WiseWebhookController
{
    public function __invoke(Request $request)
    {
        return response()->noContent();
    }
}
```

Listen for the event wherever you actually want to react to it:

```php
use Govia\WiseClient\Webhooks\Events\WiseWebhookReceived;

Event::listen(WiseWebhookReceived::class, function (WiseWebhookReceived $event) {
    match ($event->eventType) {
        'transfers#state-change' => TransferStateChanged::dispatch($event->payload),
        default => null,
    };
});
```

## Why a middleware and an event, not one class

Signature verification (`Govia\WiseClient\Webhooks\WebhookSignatureVerifier`) is a standalone, unit-testable service. The middleware is a thin adapter that plugs it into the HTTP layer. If the middleware shape doesn't fit your app — say, you want to verify inside a job instead of a route — call `WebhookSignatureVerifier::verify()` directly and skip the middleware entirely.
