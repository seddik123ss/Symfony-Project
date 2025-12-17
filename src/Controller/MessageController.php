<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository, UsersRepository $usersRepository, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $conversations = $messageRepository->findConversations($user);
        $unreadCount = $messageRepository->countUnreadByRecipient($user);
        
        // Get selected conversation
        $selectedUserId = $request->query->get('user');
        $selectedConversation = null;
        $conversationMessages = [];
        
        if ($selectedUserId) {
            $selectedUser = $usersRepository->find($selectedUserId);
            
            if ($selectedUser && isset($conversations[$selectedUserId])) {
                $selectedConversation = $conversations[$selectedUserId];
                $conversationMessages = $messageRepository->findConversationBetween($user, $selectedUser);
                
                // Mark all messages as read
                foreach ($conversationMessages as $msg) {
                    if ($msg->getRecipient()->getId() === $user->getId() && !$msg->isRead()) {
                        $msg->setIsRead(true);
                    }
                }
                $em->flush();
            }
        }

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'conversationMessages' => $conversationMessages,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/new', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UsersRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $message = new Message();
        $user = $this->getUser();
        $message->setSender($user);

        // Pre-select recipient if provided in query string
        $recipientId = $request->query->get('recipient');
        if ($recipientId) {
            $recipient = $usersRepository->find($recipientId);
            if ($recipient) {
                $message->setRecipient($recipient);
            }
        }

        $form = $this->createForm(MessageType::class, $message, [
            'current_user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sanitize content for profanity before saving
            if (class_exists(\App\Service\ProfanityFilter::class)) {
                $filter = $this->container->get(\App\Service\ProfanityFilter::class);
                $message->setContent($filter->sanitize((string) $message->getContent()));
            }
            $em->persist($message);
            $em->flush();

            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('message_index', ['user' => $message->getRecipient()->getId()]);
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message, MessageRepository $messageRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        // Security check: user must be sender or recipient
        if ($message->getSender()->getId() !== $user->getId() && 
            $message->getRecipient()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce message.');
        }

        // Mark as read if current user is the recipient
        if ($message->getRecipient()->getId() === $user->getId() && !$message->isRead()) {
            $message->setIsRead(true);
            $em->flush();
        }

        return $this->render('message/show.html.twig', [
            'message' => $message,
            'currentUser' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        // Security check
        if ($message->getSender()->getId() !== $user->getId() && 
            $message->getRecipient()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce message.');
        }

        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            // Soft delete: mark as deleted by sender or recipient
            if ($message->getSender()->getId() === $user->getId()) {
                $message->setDeletedBySender(true);
            } else {
                $message->setDeletedByRecipient(true);
            }
            
            // If both deleted, actually remove from database
            if ($message->isDeletedBySender() && $message->isDeletedByRecipient()) {
                $em->remove($message);
            }
            
            $em->flush();

            $this->addFlash('success', 'Message supprimé avec succès !');
        }

        // Redirect back to the conversation if possible
        $otherUserId = null;
        if ($message->getSender()) {
            $otherUserId = $message->getSender()->getId() === $user->getId() && $message->getRecipient() ? $message->getRecipient()->getId() : ($message->getSender() ? $message->getSender()->getId() : null);
        }

        if ($otherUserId) {
            return $this->redirectToRoute('message_index', ['user' => $otherUserId]);
        }

        return $this->redirectToRoute('message_index');
    }

    #[Route('/{id}/edit', name: 'message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        // Only sender can edit their message
        if ($message->getSender()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas la permission de modifier ce message.');
        }

        $form = $this->createForm(MessageType::class, $message, [
            'current_user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sanitize edited content
            if (class_exists(\App\Service\ProfanityFilter::class)) {
                $filter = $this->container->get(\App\Service\ProfanityFilter::class);
                $message->setContent($filter->sanitize((string) $message->getContent()));
            }
            $em->flush();

            $this->addFlash('success', 'Message mis à jour avec succès !');

            // Redirect back to the conversation with the recipient
            $otherUserId = $message->getRecipient() ? $message->getRecipient()->getId() : null;
            if ($otherUserId) {
                return $this->redirectToRoute('message_index', ['user' => $otherUserId]);
            }

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/read', name: 'message_mark_read', methods: ['POST'])]
    public function markAsRead(Message $message, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        // Security check: only recipient can mark as read
        if ($message->getRecipient()->getId() !== $user->getId()) {
            return new JsonResponse(['success' => false, 'message' => 'Accès refusé'], 403);
        }

        $message->setIsRead(true);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/send-email', name: 'message_send_email', methods: ['POST'])]
    public function sendAsEmail(Request $request, Message $message, EmailService $emailService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        // Security check: user must be sender or recipient
        if ($message->getSender()->getId() !== $user->getId() && 
            $message->getRecipient()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce message.');
        }

        // Validate CSRF token
        if ($this->isCsrfTokenValid('send_email'.$message->getId(), $request->request->get('_token'))) {
            $success = $emailService->sendMessageAsEmail($message);

            if ($success) {
                $this->addFlash('success', '📧 Email envoyé avec succès à ' . $message->getRecipient()->getEmail());
            } else {
                $this->addFlash('error', '❌ Erreur lors de l\'envoi de l\'email. Vérifiez la configuration Gmail.');
            }
        } else {
            $this->addFlash('error', '❌ Token de sécurité invalide.');
        }

        // Redirect back to the conversation
        $otherUserId = null;
        if ($message->getSender()->getId() === $user->getId()) {
            $otherUserId = $message->getRecipient()->getId();
        } else {
            $otherUserId = $message->getSender()->getId();
        }

        if ($otherUserId) {
            return $this->redirectToRoute('message_index', ['user' => $otherUserId]);
        }

        return $this->redirectToRoute('message_index');
    }
}



