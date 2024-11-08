<?php

namespace App\Form\Movie;

use App\Entity\Movie\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\File;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre du film',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Description',
                ],
            ])
            ->add('releaseDate', DateType::class, [
                'label' => false,
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Date de sortie',
                ],
            ])
            ->add('rating', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrez une note pour le film',
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '512M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/webm',
                            'video/ogg',
                            'video/x-msvideo',  // AVI
                            'video/x-matroska',  // MKV
                        ],
                        'mimeTypesMessage' => 'Please upload a valid video file (MP4, WebM, Ogg, AVI, MKV).',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
