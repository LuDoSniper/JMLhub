<?php

namespace App\Form\Movie;

use App\Entity\Movie\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('releaseDate', null, [
                'widget' => 'single_text',
            ])
            ->add('rating')
            ->add('file', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',
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
