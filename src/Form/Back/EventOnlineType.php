<?php

namespace App\Form\Back;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventOnlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 150,
                    ]),
                ],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 50,
                        'max' => 3000,
                    ])
                ],
            ])
            ->add('pictureFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => false,
            ])
            ->add('date', null, [
                'label' => 'Date de l\'évènement',
                'widget' => 'single_text',
            ])
            ->add('maxMembers', null, [
                'label' => 'Nombre maximum de participants',
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new GreaterThanOrEqual(2),
                ],
            ])
            ->add('isArchived', null, [
                'label' => 'Est archivé',
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
