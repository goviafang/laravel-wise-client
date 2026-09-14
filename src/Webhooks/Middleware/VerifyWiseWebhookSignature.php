<?php

declare(strict_types=1);

namespace Govia\WiseClient\Webhooks\Middleware;

use Closure;
use Govia\WiseClient\Webhooks\WebhookEventDispatcher;
use Govia\WiseClient\Webhooks\WebhookSignatureVerifier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class VerifyWiseWebhookSignature
{
    public function __construct(
        private readonly WebhookSignatureVerifier $verifier,
        private readonly WebhookEventDispatcher $dispatcher,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature-SHA256');

        if ($signature === null || ! $this->verifier->verify($request->getContent(), $signature)) {
            abort(401, 'Invalid Wise webhook signature.');
        }

        $this->dispatcher->dispatch($request->json()->all());

        return $next($request);
    }
}
