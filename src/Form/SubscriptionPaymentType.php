<?php

namespace App\Form;

use App\Entity\SubscriptionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionPaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subscription_type', EntityType::class, [
                'class' => SubscriptionType::class,
                'choice_label' => function (SubscriptionType $type) {
                    return sprintf('%s - %.2f€', $type->getName(), $type->getPrice());
                },
                'label' => 'Type d\'abonnement',
                'placeholder' => 'Sélectionnez un abonnement',
                'required' => true,
            ])

            ->add('card_number', TextType::class, [
                'label' => 'Numéro de carte',
                'required' => true,
                'attr' => [
                    'maxlength' => 16,
                    'pattern' => '[0-9]{16}',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 16,
                        'max' => 16,
                        'exactMessage' => 'Le numéro de carte doit contenir 16 chiffres.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Le numéro de carte doit contenir uniquement des chiffres.',
                    ])
                ]
            ])

            ->add('cvv', IntegerType::class, [
                'label' => 'CVV',
                'required' => true,
                'attr' => [
                    'maxlength' => 3,
                    'min' => 100,
                    'max' => 999,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 3]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{3}$/',
                        'message' => 'CVV invalide.',
                    ])
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }
}
