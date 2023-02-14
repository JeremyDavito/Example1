<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class EventOnlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 150,
                    ]),
                ],
            ])

            ->add('description', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 50,
                        'max' => 3000,
                    ])
                ],
            ])

            ->add('picture', null, [
                'required' => false,
                'empty_data' => 'event_placeholder.png',
            ])

            ->add('date', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThan("+ 5 hours")
                ],
            ])

            ->add('latitude', null, [
                'required' => false,
            ])

            ->add('longitude', null, [
                'required' => false,
            ])

            ->add('maxMembers', null, [
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new GreaterThanOrEqual(2),
                ],
            ])

            ->add('isOnline', ChoiceType::class, [
                'choices' => [
                    'online' => true,
                    'not online' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])

            ->add('category', null, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
