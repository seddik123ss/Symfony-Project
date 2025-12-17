<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function __construct(
        private UsersRepository $usersRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $options['current_user'] ?? null;

        $builder
            ->add('recipient', EntityType::class, [
                'class' => Users::class,
                'choice_label' => function (Users $user) {
                    return $user->getFullname() . ' (' . $user->getEmail() . ')';
                },
                'query_builder' => function (UsersRepository $er) use ($currentUser) {
                    $qb = $er->createQueryBuilder('u')
                        ->where('u.isActive = true');
                    
                    if ($currentUser) {
                        $qb->andWhere('u.id != :currentUser')
                           ->setParameter('currentUser', $currentUser->getId());
                    }
                    
                    return $qb->orderBy('u.fullname', 'ASC');
                },
                'placeholder' => 'Sélectionnez un destinataire',
                'label' => 'Destinataire',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le sujet du message'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                    'placeholder' => 'Écrivez votre message ici...'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'current_user' => null,
        ]);
    }
}



