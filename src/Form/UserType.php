<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // minimum datas to create a user
        $builder
            ->add('email', null, [
                'constraints' => [
                    new Email([
                        'mode' => 'html5',
                    ]),
                    new NotBlank(),
                ]
            ])
            ->add('roles', null, [
                'empty_data' => []
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'constraints' => [
                    new Regex([
                        // check for at least 1 digit, 1 lower and 1 upper character, 1 special character and 8 characters
                        'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/', 
                    ])
                ]
            ])
            ->add('firstname', null, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('lastname', null, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])

            // add some fields when editing a user
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $event->getData();

                if ($user->getId() != null) {
                    $form
                        ->add('phoneNumber', null, [
                            'required' => false,
                            'constraints' => [
                                new Length([
                                    'min' => 10,
                                    'max' => 10,
                                ])
                            ]
                        ])
//                        ->add('avatar', null, [
//                            'required' => false,
//                        ])
                        ->add('address', null, [
                            'required' => false,
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('zipcode', null, [
                            'required' => false,
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('city', null, [
                            'required' => false,
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('country', null, [
                            'required' => false,
                            'constraints' => [
                                new NotBlank(),
                            ],
                        ])
                        ->add('latitude', null, [
                            'required' => false,
                        ])
                        ->add('longitude', null, [
                            'required' => false,
                        ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
