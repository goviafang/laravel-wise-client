# laravel-wise-client

給 Laravel 用的 [Wise Platform API](https://wise.com) client，涵蓋核心匯款流程：profile、quote、recipient、transfer、balance、activity、匯率查詢與 webhook。

English documentation: [README.md](README.md)。

## 開始之前

這個套件只負責跟 Wise 的 API 溝通，沒辦法幫你拿到存取權限。要先在 Wise 那邊把憑證準備好。

**Sandbox（開發測試用）**
1. 到 [sandbox.transferwise.tech/register](https://sandbox.transferwise.tech/register) 申請免費的 sandbox 帳號——只要 email 跟密碼，不用驗證，帳號會預先帶 1,000,000 GBP 的測試金額。
2. 登入後到 **Settings → API tokens** 產生一組 personal token。Sandbox 的 2FA 驗證碼固定是 `111111`。
3. 填進 `.env` 的 `WISE_PERSONAL_TOKEN`，並設 `WISE_ENVIRONMENT=sandbox`。

**正式環境（Production）**
1. 申請一個真正的 Wise 帳號，並開啟兩步驟驗證——這是產生 token 的前提。
2. 到 [wise.com](https://wise.com) 的 **Settings → API tokens** 產生 personal token（手機 app 沒有這個功能）。
3. 把 `WISE_ENVIRONMENT` 改成 `production`，換成正式環境的 token。

**OAuth（`client_credentials` / `user_token` 驅動）**
`client_id`/`client_secret` 不是自助申請的，是 Wise 走完合作夥伴流程後才會發給你。細節看 [Wise Platform API 文件](https://docs.wise.com/api-reference) 跟 [上線檢查清單](https://docs.wise.com/guides/product/kyc/partner-accounts#go-live)。如果你只是串自己的個人或商業帳戶，上面的 personal token 就夠用，這段可以跳過。

**Webhook**
簽章走 RSA/SHA256 對原始 body 簽，設定 `WISE_WEBHOOK_PUBLIC_KEY` 之前，先到 [webhook event 文件](https://docs.wise.com/api-reference/webhook-event) 確認目前 sandbox／正式環境各自對應的公鑰。

## 安裝

```bash
composer require goviafang/laravel-wise-client
```

發布設定檔：

```bash
php artisan vendor:publish --tag=wise-config
```

依你要用的認證方式在 `.env` 填入對應憑證：

```env
WISE_ENVIRONMENT=sandbox
WISE_AUTH_DRIVER=personal_token
WISE_PERSONAL_TOKEN=你的個人 token
```

## 快速開始

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

不想用 Builder 也可以，底層一樣是這些方法，自己一步步呼叫也行：

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

## 文件

- [安裝與設定](docs/zh-TW/installation.md)
- [認證方式](docs/zh-TW/authentication.md)
- [各資源用法](docs/zh-TW/resources.md)
- [Webhook](docs/zh-TW/webhooks.md)
- [錯誤處理](docs/zh-TW/errors.md)

## 涵蓋範圍

這個套件只做核心匯款流程。卡片發行、消費限額、爭議處理，以及 PIN／臉部辨識／裝置指紋這些生物辨識 SCA 流程不在範圍內，完整清單見 [docs/zh-TW/resources.md](docs/zh-TW/resources.md)。

## 測試

```bash
composer test
composer stan
composer format
```

## License

MIT。
