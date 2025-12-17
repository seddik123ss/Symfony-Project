<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('clientName', TextType::class, [
                'label' => 'Nom complet',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom complet'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le nom est obligatoire.'),
                    new Assert\Length(min: 2, max: 255)
                ]
            ])
            ->add('clientEmail', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'votre@email.com'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'L\'email est obligatoire.'),
                    new Assert\Email(message: 'L\'email n\'est pas valide.')
                ]
            ])
            ->add('clientPhone', TelType::class, [
                'label' => 'Téléphone (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+33 6 12 34 56 78'
                ]
            ])
            ->add('offeredAmount', NumberType::class, [
                'label' => 'Montant offert (€)',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00',
                    'step' => '0.01',
                    'min' => '0.01'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le montant est obligatoire.'),
                    new Assert\GreaterThan(value: 0, message: 'Le montant doit être supérieur à 0.')
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Votre message au vendeur...'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le message est obligatoire.'),
                    new Assert\Length(min: 10, max: 2000)
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}

