<?php

namespace App\Form;

use App\Entity\Alert;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
