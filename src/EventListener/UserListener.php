<?php

namespace App\EventListener;

use App\Entity\Authentication\User;
use App\Service\TokenService;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserListener
{
    private TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Génére un token pour la réinitialisation de mot de passe avant la création de l'utilisateur.
     */
    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        // Génère un token pour la réinitialisation de mot de passe
        $token = $this->tokenService->generateExpiringToken($user->getId());

        // Assigne le token de réinitialisation à l'utilisateur
        $user->setResetToken($token);
    }
}
