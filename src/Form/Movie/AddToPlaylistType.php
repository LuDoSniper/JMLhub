<?php

namespace App\Form\Movie;

use App\Entity\Movie\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToPlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('playlists', ChoiceType::class, [
            'choices' => $options['playlists'],
            'multiple' => true,
            'expanded' => true,
            'choice_label' => function (Playlist $playlist) use ($options) {
                return $playlist->getName();
            },
            'data' => $options['selectedPlaylists'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'playlists' => [],
            'selectedPlaylists' => [],
        ]);
    }
}