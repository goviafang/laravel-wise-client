# Webhook

## 訂閱

```php
Wise::webhooks()->subscribe($profileId, new CreateWebhookSubscriptionData(
    name: 'Transfer updates',
    triggerOn: 'transfers#state-change',
    deliveryUrl: 'https://your-app.test/wise/webhook',
));

Wise::webhooks()->list($profileId);
Wise::webhooks()->unsubscribe($profileId, $subscriptionId);
```

Wise 自己的 API 文件列出完整的 event type 清單（`transfers#state-change`、`balances#update`、`profiles#state-change` 等），套件這邊沒有把它們寫死——`triggerOn` 要對應到你的 app 怎麼反應，這是你的 app 該決定的事，不屬於 client 套件的責任。

## 接收

每個 webhook 呼叫都會帶 `X-Signature-SHA256`，是對原始 body 做的 RSA-SHA256 簽章，要拿你設定 subscription 時 Wise 給的公鑰驗證。放進 `.env`：

```env
WISE_WEBHOOK_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----"
```

掛上套件內建的 middleware：

```php
Route::post('/wise/webhook', WiseWebhookController::class)
    ->middleware('wise.verify-signature');
```

簽章驗不過會直接回 `401`；驗過之後，middleware 會把解析好的 payload 包成 `Govia\WiseClient\Webhooks\Events\WiseWebhookReceived` 這個 Laravel event 送出去。你的 controller 什麼都不用做，回個 `200` 就好：

```php
class WiseWebhookController
{
    public function __invoke(Request $request)
    {
        return response()->noContent();
    }
}
```

真正要處理事件的地方，用 Listener 訂閱：

```php
use Govia\WiseClient\Webhooks\Events\WiseWebhookReceived;

Event::listen(WiseWebhookReceived::class, function (WiseWebhookReceived $event) {
    match ($event->eventType) {
        'transfers#state-change' => TransferStateChanged::dispatch($event->payload),
        default => null,
    };
});
```

## 為什麼分成 middleware 跟 event，不是一個類別包到底

簽章驗證（`Govia\WiseClient\Webhooks\WebhookSignatureVerifier`）是一個獨立、能單元測試的 service。Middleware 只是把它接到 HTTP 層的一層薄轉接。如果 middleware 這種形狀不適合你的架構——比如你想在 job 裡驗證而不是在 route 層——直接呼叫 `WebhookSignatureVerifier::verify()`，不用經過 middleware 也可以。
