# Resources & usage

Every resource is reachable off `Wise::` (the facade) or `app(Govia\WiseClient\WiseClient::class)` if you'd rather inject it. Each resource method delegates to a single-purpose Command or Query class under `src/Commands` / `src/Queries` — you can use those directly too if you're building something that doesn't fit the facade shape.

## Profiles

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

Wise's personal and business profile creation endpoints take genuinely different payloads (not one endpoint with a `type` switch), which is why there are two DTOs and two commands instead of one generic one. Optional fields beyond the required ones go through the `$extra` array on either DTO and are passed straight through.

## Quotes

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

## Recipients

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
    // lazily paginated — fetches the next page only when you keep iterating
}
```

`details` is intentionally an untyped array here: the required fields inside it depend on the currency and route, and Wise expects you to check the account-requirements endpoint (not covered by this package) to know what's required for a given corridor.

## Transfers

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

`customerTransactionId` is how Wise de-duplicates retried requests — there's no unified idempotency header across the API, so each write endpoint that needs it uses its own body field like this one.

For the create-quote → pick-recipient → create-transfer → fund sequence, `Wise::transferBuilder($profileId)` chains the same calls — see the README quick start.

## Balances

```php
Wise::balances()->create($profileId, currency: 'EUR', idempotenceUuid: (string) Str::uuid());
Wise::balances()->list($profileId);
Wise::balances()->get($profileId, $balanceId);
Wise::balances()->statement($profileId, $balanceId, $from, $to); // returns the raw Response, statement formats vary
```

## Activity

```php
foreach (Wise::activities()->list($profileId) as $activity) {
    // cursor-paginated
}
```

## Exchange rates

```php
Wise::rates()->get(source: 'GBP', target: 'USD');
```

## Webhooks

See [webhooks.md](webhooks.md).

## Out of scope

Deliberately not implemented in this release, to keep the package's surface area matched to what it actually does well:

- Card issuing, spend limits, spend controls, disputes, card orders
- Biometric SCA (PIN, face scan, device fingerprint) — the package only detects and surfaces the challenge, see [authentication.md](authentication.md)
- KYC review, verification documents, directors/UBOs endpoints
- Simulations, batch groups, multi-currency account management beyond balances

Every one of these is a real Wise endpoint group; they're excluded because they serve a different kind of integration (card programs, compliance tooling) than a straightforward "send money" client. If you need one of them, the `Http\HttpClientInterface` and `Auth\` layers are reusable — you'd add a new `Resources\`/`Commands\`/`Queries\` set following the same pattern as the existing ones.
