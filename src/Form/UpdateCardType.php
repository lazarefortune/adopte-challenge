<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('card_number', TextType::class, [
                'label' => 'Numéro de carte',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 16, 'max' => 16]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Le numéro de carte doit contenir uniquement des chiffres.',
                    ]),
                ],
                'attr' => ['class' => 'form-input'],
            ])

            ->add('cvv', IntegerType::class, [
                'label' => 'CVV',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 3]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{3}$/',
                        'message' => 'CVV invalide.',
                    ])
                ],
                'attr' => ['class' => 'form-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
