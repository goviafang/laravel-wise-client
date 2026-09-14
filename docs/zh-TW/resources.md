# 各資源用法

每個資源都掛在 `Wise::`（facade）底下，也可以直接注入 `app(Govia\WiseClient\WiseClient::class)`。每個 Resource 方法底層都是轉呼叫 `src/Commands` / `src/Queries` 底下的單一職責類別，如果 facade 的形狀不合用，也可以直接拿那些類別來用。

## Profile

```php
Wise::profiles()->createPersonal(new CreatePersonalProfileData(
    firstName: 'Olivia',
    lastName: 'Wilson',
    dateOfBirth: '1990-01-01',
    address: ['addressFirstLine' => '24 Willow Creek Lane', 'city' => 'Bristol', 'countryIso3Code' => 'gbr'],
    contactDetails: ['email' => 'olivia@example.com', 'phoneNumber' => '+441234567890'],
));

Wise::profiles()->createBusiness(new CreateBusinessProfileData(businessName: 'ABC Logistics Ltd'));

Wise::profiles()->get($profileId);
Wise::profiles()->list();
```

Wise 的個人／商業 profile 建立本來就是兩個完全不同 payload 的 endpoint（不是同一個 endpoint 用 `type` 欄位切換），所以這裡對應兩個 DTO 跟兩個 Command，沒有硬湊成一個通用的。必填欄位以外的選填欄位，透過兩個 DTO 上的 `$extra` 陣列直接透傳給 Wise。

## Quote

```php
Wise::quotes()->create(new CreateQuoteData(
    profileId: $profileId,
    sourceCurrency: 'GBP',
    targetCurrency: 'USD',
    sourceAmount: 100.0,
));

Wise::quotes()->get($profileId, $quoteId);
Wise::quotes()->update($profileId, $quoteId, targetRecipientId: $recipientId);
```

## Recipient

```php
Wise::recipients()->create(new CreateRecipientData(
    profileId: $profileId,
    accountHolderName: 'John Doe',
    currency: 'GBP',
    type: 'sort_code',
    details: ['sortCode' => '040075', 'accountNumber' => '37778842'],
));

Wise::recipients()->get($recipientId);
Wise::recipients()->delete($recipientId);

foreach (Wise::recipients()->list($profileId) as $recipient) {
    // 惰性分頁，持續 foreach 才會去抓下一頁
}
```

`details`故意設計成沒有型別的陣列：裡面到底要填哪些欄位，取決於幣別跟匯款路線，Wise 要求你查 account-requirements 這個 endpoint（不在這個套件的涵蓋範圍內）才知道特定路線需要什麼。

## Transfer

```php
$transfer = Wise::transfers()->create(new CreateTransferData(
    quoteId: $quote->id,
    targetRecipientId: $recipientId,
    customerTransactionId: (string) Str::uuid(),
));

Wise::transfers()->fund($profileId, $transfer->id);
Wise::transfers()->cancel($transfer->id);
Wise::transfers()->get($transferId);

foreach (Wise::transfers()->list($profileId) as $transfer) {
    // ...
}
```

`customerTransactionId` 是 Wise 用來去重重試請求的欄位——整個 API 沒有統一的 idempotency header，所以每個需要去重的寫入 endpoint 都各自用自己的 body 欄位處理。

「建立 quote → 選 recipient → 建立 transfer → 撥款」這串流程，`Wise::transferBuilder($profileId)` 幫你串起來，範例見 README 的快速開始。

## Balance

```php
Wise::balances()->create($profileId, currency: 'EUR', idempotenceUuid: (string) Str::uuid());
Wise::balances()->list($profileId);
Wise::balances()->get($profileId, $balanceId);
Wise::balances()->statement($profileId, $balanceId, $from, $to); // 回傳原始 Response，對帳單格式不只一種
```

## Activity

```php
foreach (Wise::activities()->list($profileId) as $activity) {
    // cursor 分頁
}
```

## 匯率

```php
Wise::rates()->get(source: 'GBP', target: 'USD');
```

## Webhook

見 [Webhook](webhooks.md)。

## 不在涵蓋範圍內

刻意不做的部分，目的是讓套件的範圍跟它實際擅長的事情對齊：

- 卡片發行、消費限額、消費控制、爭議處理、卡片訂購
- 生物辨識 SCA（PIN、臉部辨識、裝置指紋）——套件只偵測並拋出挑戰例外，見 [認證方式](authentication.md)
- KYC 審核、驗證文件、董事／實質受益人（UBO）相關 endpoint
- 模擬測試（simulation）、批次群組、balance 以外的多幣別帳戶管理

上面每一項都是 Wise 真實存在的 endpoint 群組，排除的原因是它們服務的是不同性質的整合（卡片發行方案、法遵工具），跟單純的「匯款」client 不是同一回事。如果你真的需要用到，`Http\HttpClientInterface` 跟 `Auth\` 這兩層可以直接沿用，照現有的模式加一組新的 `Resources\`/`Commands\`/`Queries\` 就行。
