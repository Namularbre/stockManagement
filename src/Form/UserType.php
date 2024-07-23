<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-input'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new Blank([
                        'message' => 'Please enter a username',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Username need to be at least 3 characters',
                        'max' => 254,
                        'maxMessage' => 'Username must be less that 254 characters',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'User' => 'ROLE_USER',
                        'Admin' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    'expanded' => true,
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-input'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new Blank([
                        'message' => 'Please enter a username',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Username need to be at least 3 characters',
                        'max' => 254,
                        'maxMessage' => 'Username must be less that 254 characters',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
