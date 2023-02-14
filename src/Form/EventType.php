<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class EventType extends AbstractType
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

//            ->add('picture', FileType::class, [
//                'required' => false,
//            ])

            ->add('date', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThan("+ 5 hours")
                ],
            ])

            ->add('address', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
            ])

            ->add('zipcode', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 5,
                        'max' => 5,
                    ])
                ],
            ])

            ->add('city', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
            ])

            ->add('country', null, [
                'required' => true,
                'empty_data' => 'France',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
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
