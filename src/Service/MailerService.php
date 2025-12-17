<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Sale;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly string $fromEmail,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    /**
     * Envoie une notification de réception d'offre au vendeur
     */
    public function sendOfferReceivedNotification(Offer $offer, Sale $sale): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($sale->getContactInfo() ?: $this->fromEmail)
                ->subject('Nouvelle offre reçue - ' . $sale->getTitle())
                ->html($this->twig->render('emails/offer_received.html.twig', [
                    'offer' => $offer,
                    'sale' => $sale,
                ]));

            $this->mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas le processus
            error_log('Erreur envoi email offre reçue: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification de changement de statut
     */
    public function sendStatusUpdatedNotification(Sale $sale, string $recipientEmail, string $recipientName, string $oldStatus, string $newStatus): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($recipientEmail)
                ->subject('Mise à jour du statut - ' . $sale->getTitle())
                ->html($this->twig->render('emails/status_updated.html.twig', [
                    'sale' => $sale,
                    'recipientName' => $recipientName,
                    'oldStatus' => $oldStatus,
                    'newStatus' => $newStatus,
                ]));

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email statut mis à jour: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification d'offre acceptée au client
     */
    public function sendOfferAcceptedNotification(Offer $offer, Sale $sale): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($offer->getClientEmail())
                ->subject('Votre offre a été acceptée - ' . $sale->getTitle())
                ->html($this->twig->render('emails/offer_accepted.html.twig', [
                    'offer' => $offer,
                    'sale' => $sale,
                ]));

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email offre acceptée: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification d'offre refusée au client
     */
    public function sendOfferRefusedNotification(Offer $offer, Sale $sale): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($offer->getClientEmail())
                ->subject('Votre offre a été refusée - ' . $sale->getTitle())
                ->html($this->twig->render('emails/offer_refused.html.twig', [
                    'offer' => $offer,
                    'sale' => $sale,
                ]));

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email offre refusée: ' . $e->getMessage());
        }
    }

    /**
     * Envoie une notification de paiement confirmé
     */
    public function sendPaymentConfirmedNotification(Sale $sale, string $recipientEmail, string $recipientName, float $amount): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($recipientEmail)
                ->subject('Paiement confirmé - ' . $sale->getTitle())
                ->html($this->twig->render('emails/payment_confirmed.html.twig', [
                    'sale' => $sale,
                    'recipientName' => $recipientName,
                    'amount' => $amount,
                ]));

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email paiement confirmé: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email de test (pour l'API)
     */
    public function sendTestEmail(string $to, string $subject, string $message): void
    {
        try {
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($to)
                ->subject($subject)
                ->html('<h1>Test Email ArtEdu</h1><p>' . htmlspecialchars($message) . '</p>');

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email test: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoie une confirmation d'achat direct avec facture
     */
    public function sendDirectPurchaseConfirmation(
        Sale $sale,
        string $clientName,
        string $clientEmail,
        float $amount,
        string $invoicePath
    ): void {
        try {
            $invoiceFullPath = $this->parameterBag->get('kernel.project_dir') . '/public/' . $invoicePath;
            
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($clientEmail)
                ->subject('Confirmation d\'achat - ' . $sale->getTitle())
                ->html($this->twig->render('emails/direct_purchase_confirmation.html.twig', [
                    'sale' => $sale,
                    'clientName' => $clientName,
                    'amount' => $amount,
                    'invoicePath' => $invoicePath,
                ]));

            // Attacher la facture PDF si elle existe
            if (file_exists($invoiceFullPath)) {
                $email->attachFromPath($invoiceFullPath, 'facture.pdf', 'application/pdf');
            }

            $this->mailer->send($email);
        } catch (\Exception $e) {
            error_log('Erreur envoi email confirmation achat direct: ' . $e->getMessage());
        }
    }

}

