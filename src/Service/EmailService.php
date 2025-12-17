<?php

namespace App\Service;

use App\Entity\Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {
    }

    public function sendMessageAsEmail(Message $message): bool
    {
        try {
            // Use Gmail address from environment variable
            // Extract email from MAILER_DSN or use a default
            $senderEmail = $_ENV['GMAIL_SENDER_EMAIL'] ?? 'your-email@gmail.com';
            $senderName = $_ENV['GMAIL_SENDER_NAME'] ?? 'Système de Messages';
            
            $email = (new Email())
                ->from(new Address($senderEmail, $senderName))
                ->to(new Address($message->getRecipient()->getEmail(), $message->getRecipient()->getFullname()))
                ->subject('📧 ' . $message->getSubject())
                ->html($this->twig->render('emails/message.html.twig', [
                    'message' => $message,
                    'sender' => $message->getSender(),
                    'recipient' => $message->getRecipient()
                ]));

            $this->mailer->send($email);
            return true;
        } catch (\Exception $e) {
            // Log detailed error for debugging
            error_log('Email sending failed: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}