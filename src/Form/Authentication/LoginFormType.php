<?php

namespace App\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_email', EmailType::class, [
                'label' => 'Login',
                //'placeholder' => 'Email or Username',
                'required' => true,
            ])
            ->add('_username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'required' => true,
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe',
                //'placeholder' => 'Mot de passe',
                'required' => true,
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Connexion'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
