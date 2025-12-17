<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Find all messages sent by a user
     */
    public function findBySender(Users $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->andWhere('m.deletedBySender = false')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all messages received by a user
     */
    public function findByRecipient(Users $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.recipient = :user')
            ->andWhere('m.deletedByRecipient = false')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count unread messages for a user
     */
    public function countUnreadByRecipient(Users $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.recipient = :user')
            ->andWhere('m.isRead = false')
            ->andWhere('m.deletedByRecipient = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find a message that belongs to a user (either as sender or recipient)
     */
    public function findOneByUser(Message $message, Users $user): ?Message
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->andWhere('(m.sender = :user OR m.recipient = :user)')
            ->setParameter('id', $message->getId())
            ->setParameter('user', $user);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get all conversations for a user (grouped by other user)
     */
    public function findConversations(Users $user): array
    {
        // Get all messages where user is sender or recipient and not deleted by user
        $messages = $this->createQueryBuilder('m')
            ->where('(m.sender = :user OR m.recipient = :user)')
            ->andWhere('(m.sender = :user AND m.deletedBySender = false) OR (m.recipient = :user AND m.deletedByRecipient = false)')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $conversations = [];
        foreach ($messages as $message) {
            // Determine the other user in the conversation
            $otherUser = $message->getSender()->getId() === $user->getId() 
                ? $message->getRecipient() 
                : $message->getSender();
            
            $otherUserId = $otherUser->getId();
            
            // Initialize conversation if not exists
            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [
                    'user' => $otherUser,
                    'lastMessage' => $message,
                    'unreadCount' => 0,
                    'messages' => []
                ];
            }
            
            // Add message to conversation
            $conversations[$otherUserId]['messages'][] = $message;
            
            // Count unread (only if current user is recipient)
            if ($message->getRecipient()->getId() === $user->getId() && !$message->isRead()) {
                $conversations[$otherUserId]['unreadCount']++;
            }
        }

        // Sort conversations by last message date
        uasort($conversations, function($a, $b) {
            return $b['lastMessage']->getCreatedAt() <=> $a['lastMessage']->getCreatedAt();
        });

        return $conversations;
    }

    /**
     * Get conversation between two users
     */
    public function findConversationBetween(Users $user1, Users $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('((m.sender = :user1 AND m.recipient = :user2) OR (m.sender = :user2 AND m.recipient = :user1))')
            ->andWhere('(m.sender = :user1 AND m.deletedBySender = false) OR (m.recipient = :user1 AND m.deletedByRecipient = false)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}



