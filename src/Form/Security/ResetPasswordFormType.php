<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Merci de renseigner votre mot de passe']),
                ]
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Confirmer',
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event) {
            $form = $event->getForm();

            // Récupère les mots de passe
            $password = $form->get('password')->getData();
            $confirmPassword = $form->get('confirm_password')->getData();

            // Vérifie s'ils correspondent
            if ($password !== $confirmPassword) {
                $form->get('confirm_password')->addError(new FormError('Les mots de passe ne correspondent pas.'));
            }
        });
    }
}
