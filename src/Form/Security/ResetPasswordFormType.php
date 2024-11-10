<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre mot de passe']),
                ]
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre mot de passe']),
                ]
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Confirmer',
            ]);
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormInterface $form) {
                $password = $form->get('password')->getData();
                $confirmPassword = $form->get('confirm_password')->getData();

                if ($password !== $confirmPassword) {
                    // Ajoute une erreur sur le champ confirm_password si les mots de passe ne correspondent pas
                    $form->get('confirm_password')->addError(
                        new FormError('Les mots de passe ne correspondent pas.')
                    );
                }
            }
        );
    }
}
