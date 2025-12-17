<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messenger')]
class MessengerController extends AbstractController
{
    #[Route('/', name: 'messenger_index', methods: ['GET'])]
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

        return $this->render('messenger/index.html.twig', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'conversationMessages' => $conversationMessages,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/new', name: 'messenger_new', methods: ['GET', 'POST'])]
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
            $em->persist($message);
            $em->flush();

            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('messenger_index', ['user' => $message->getRecipient()->getId()]);
        }

        return $this->render('messenger/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'messenger_show', methods: ['GET'])]
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

        return $this->render('messenger/show.html.twig', [
            'message' => $message,
            'currentUser' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'messenger_delete', methods: ['POST'])]
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

        return $this->redirectToRoute('messenger_index');
    }

    #[Route('/{id}/read', name: 'messenger_mark_read', methods: ['POST'])]
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
}



