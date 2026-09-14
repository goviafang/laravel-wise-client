<?php

declare(strict_types=1);

namespace Govia\WiseClient;

use Govia\WiseClient\Builders\TransferBuilder;
use Govia\WiseClient\Http\HttpClientInterface;
use Govia\WiseClient\Resources\ActivityResource;
use Govia\WiseClient\Resources\BalanceResource;
use Govia\WiseClient\Resources\ProfileResource;
use Govia\WiseClient\Resources\QuoteResource;
use Govia\WiseClient\Resources\RateResource;
use Govia\WiseClient\Resources\RecipientResource;
use Govia\WiseClient\Resources\TransferResource;
use Govia\WiseClient\Resources\WebhookResource;

final class WiseClient
{
    public function __construct(private readonly HttpClientInterface $http) {}

    public function profiles(): ProfileResource
    {
        return new ProfileResource($this->http);
    }

    public function quotes(): QuoteResource
    {
        return new QuoteResource($this->http);
    }

    public function recipients(): RecipientResource
    {
        return new RecipientResource($this->http);
    }

    public function transfers(): TransferResource
    {
        return new TransferResource($this->http);
    }

    public function balances(): BalanceResource
    {
        return new BalanceResource($this->http);
    }

    public function activities(): ActivityResource
    {
        return new ActivityResource($this->http);
    }

    public function rates(): RateResource
    {
        return new RateResource($this->http);
    }

    public function webhooks(): WebhookResource
    {
        return new WebhookResource($this->http);
    }

    public function transferBuilder(int $profileId): TransferBuilder
    {
        return new TransferBuilder($this->http, $profileId);
    }
}
