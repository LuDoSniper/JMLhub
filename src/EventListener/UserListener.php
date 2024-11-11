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
    public function prePersist(LifecycleEventArgs $event): void
    {
        // Récupère l'entité en cours de persistance
        $entity = $event->getObject();

        // Vérifie que l'entité est bien un utilisateur
        if (!$entity instanceof User) {
            return;
        }

        // Génère un token pour la réinitialisation de mot de passe
        $token = $this->tokenService->generateExpiringToken($entity->getId());

        // Assigne le token de réinitialisation à l'utilisateur
        $entity->setResetToken($token);
    }
}
