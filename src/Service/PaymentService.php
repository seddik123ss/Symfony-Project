<?php

namespace App\Service;

use App\Entity\Sale;
use App\Entity\Offer;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    private StripeClient $stripe;

    public function __construct(
        private readonly string $stripeSecretKey
    ) {
        $this->stripe = new StripeClient($this->stripeSecretKey);
    }

    /**
     * Crée un PaymentIntent pour une vente
     * 
     * @return array{clientSecret: string, paymentIntentId: string}
     * @throws ApiErrorException
     */
    public function createPaymentIntent(Sale $sale, Offer $offer, string $currency = 'eur'): array
    {
        $amount = $offer->getVendorAmount() ?? $offer->getOfferedAmount() ?? $sale->getAmount() ?? 0;
        $amountInCents = (int)($amount * 100);

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amountInCents,
            'currency' => $currency,
            'metadata' => [
                'sale_id' => $sale->getId(),
                'offer_id' => $offer->getId(),
                'sale_title' => $sale->getTitle(),
                'client_email' => $offer->getClientEmail(),
                'direct_purchase' => 'false',
            ],
            'description' => 'Paiement pour: ' . $sale->getTitle(),
        ]);

        return [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
        ];
    }

    /**
     * Crée un PaymentIntent pour un achat direct (sans offre)
     * 
     * @return array{clientSecret: string, paymentIntentId: string}
     * @throws ApiErrorException
     */
    public function createDirectPurchaseIntent(
        Sale $sale,
        float $amount,
        string $clientName,
        string $clientEmail,
        string $currency = 'eur'
    ): array {
        $amountInCents = (int)($amount * 100);

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amountInCents,
            'currency' => $currency,
            'metadata' => [
                'sale_id' => $sale->getId(),
                'sale_title' => $sale->getTitle(),
                'client_name' => $clientName,
                'client_email' => $clientEmail,
                'direct_purchase' => 'true',
            ],
            'description' => 'Achat direct: ' . $sale->getTitle(),
        ]);

        return [
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
        ];
    }

    /**
     * Confirme un paiement
     * 
     * @return bool
     */
    public function confirmPayment(string $paymentIntentId): bool
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            
            return $paymentIntent->status === 'succeeded';
        } catch (ApiErrorException $e) {
            error_log('Erreur Stripe: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les détails d'un PaymentIntent
     */
    public function getPaymentIntent(string $paymentIntentId): ?object
    {
        try {
            return $this->stripe->paymentIntents->retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            error_log('Erreur Stripe: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Annule un PaymentIntent
     */
    public function cancelPaymentIntent(string $paymentIntentId): bool
    {
        try {
            $this->stripe->paymentIntents->cancel($paymentIntentId);
            return true;
        } catch (ApiErrorException $e) {
            error_log('Erreur Stripe: ' . $e->getMessage());
            return false;
        }
    }
}

