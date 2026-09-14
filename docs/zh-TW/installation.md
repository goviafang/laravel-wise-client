# 安裝與設定

## 前置準備

用這個套件之前要先跟 Wise 拿到憑證——它是個 client，沒辦法幫你跳過 Wise 自己的申請流程。

- **Sandbox 帳號**——到 [sandbox.transferwise.tech/register](https://sandbox.transferwise.tech/register) 免費申請（email + 密碼，不用驗證，會預先帶 1,000,000 GBP 測試金額）。Sandbox 的 2FA 驗證碼固定是 `111111`。
- **Personal token**——不管哪個環境，都到帳號的 **Settings → API tokens** 產生。前提是帳號要先開兩步驟驗證；手機 app 沒有這個功能。
- **OAuth 憑證**（`client_id`/`client_secret`，只有用 `client_credentials` 或 `user_token` 驅動才需要）——不是自助申請，是 Wise 走完合作夥伴流程後才會發給你。見 [API 文件](https://docs.wise.com/api-reference) 跟 [上線檢查清單](https://docs.wise.com/guides/product/kyc/partner-accounts#go-live)。只串自己帳戶的話可以跳過。
- **Webhook 公鑰**（只有用到 webhook 才需要）——到 [webhook event 文件](https://docs.wise.com/api-reference/webhook-event) 確認目前 sandbox／正式環境各自的公鑰。

完整細節見 [認證方式](authentication.md)。

## 環境需求

- PHP 8.2 以上
- Laravel 10、11 或 12

## 安裝

```bash
composer require goviafang/lib-wise-client
```

Service provider 跟 `Wise` facade 會自動被 Laravel 探索到，不用手動註冊。想直接改設定檔而不是全靠環境變數的話，發布出來：

```bash
php artisan vendor:publish --tag=wise-config
```

會把 `config/wise.php` 複製到你的專案裡。

## 環境切換

`WISE_ENVIRONMENT` 決定要打哪個 Wise 伺服器：

- `sandbox`（預設）→ `https://api.wise-sandbox.com`
- `production` → `https://api.wise.com`

這個值會跟 `WISE_API_VERSION`（預設 `2026Q3`）組成完整的 base URL。Wise 每一季會往前推進這個版號，遇到的話直接改環境變數就好，不用等套件更新：

```env
WISE_ENVIRONMENT=sandbox
WISE_API_VERSION=2026Q3
```

## HTTP 行為

```env
WISE_HTTP_TIMEOUT=30
WISE_RETRY_ON_RATE_LIMIT=false
WISE_RETRY_MAX_ATTEMPTS=3
```

Wise 對於超過速率限制只會回 `429` 加一個 `Retry-After` header，沒有更多資訊。自動重試預設關閉，因為像建立 transfer 這種有副作用的寫入操作，靜默重試的風險可能比直接讓例外拋出來還大。如果你評估過風險可以接受（通常查詢類 API 沒問題），再打開 `WISE_RETRY_ON_RATE_LIMIT`。

認證相關的環境變數見 [認證方式](authentication.md)。
