<?php

namespace App\Twig;

use App\Repository\MessageRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageExtension extends AbstractExtension
{
    public function __construct(
        private MessageRepository $messageRepository,
        private Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unread_messages_count', [$this, 'getUnreadCount']),
        ];
    }

    public function getUnreadCount(): int
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return 0;
        }

        return $this->messageRepository->countUnreadByRecipient($user);
    }
}



