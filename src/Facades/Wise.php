<?php

declare(strict_types=1);

namespace Govia\WiseClient\Facades;

use Govia\WiseClient\Resources\ActivityResource;
use Govia\WiseClient\Resources\BalanceResource;
use Govia\WiseClient\Resources\ProfileResource;
use Govia\WiseClient\Resources\QuoteResource;
use Govia\WiseClient\Resources\RateResource;
use Govia\WiseClient\Resources\RecipientResource;
use Govia\WiseClient\Resources\TransferResource;
use Govia\WiseClient\Resources\WebhookResource;
use Govia\WiseClient\WiseClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ProfileResource profiles()
 * @method static QuoteResource quotes()
 * @method static RecipientResource recipients()
 * @method static TransferResource transfers()
 * @method static BalanceResource balances()
 * @method static ActivityResource activities()
 * @method static RateResource rates()
 * @method static WebhookResource webhooks()
 * @method static \Govia\WiseClient\Builders\TransferBuilder transferBuilder(int $profileId)
 *
 * @see WiseClient
 */
final class Wise extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WiseClient::class;
    }
}
