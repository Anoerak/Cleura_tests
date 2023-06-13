<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                null,
                [
                    'label' => 'Title',
                    'attr' => [
                        'placeholder' => 'Enter your title here',
                    ],
                ]
            )
            ->add(
                'path',
                TextareaType::class,
                [
                    'label' => 'Message',
                    'attr' => [
                        'placeholder' => 'Enter your message here',
                        'rows' => 10,
                        'cols' => 50,
                    ],
                ]
            );;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
