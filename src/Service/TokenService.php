<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TokenService
{

    private string $secretKey;

    /**
     * Récupère la clé secrète depuis les paramètres de l'application.
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag){
        $this->secretKey = $parameterBag->get('TOKEN_SECRET');
    }

    /**
     * Génère un token avec expiration pour un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur
     * @param int $expiryTime Durée de vie du token en secondes (par défaut : 3600 secondes = 1 heure)
     *
     * @return string Le token généré, encodé en base64
     */
    public function generateExpiringToken($userId,$expiryTime = 360): string {
        $expiration = time() + $expiryTime;
        $data = $userId . '|' . $expiration;

        $signature = hash_hmac('sha256', $data, $this->secretKey);

        return base64_encode($data . '|' . $signature);
    }

    /**
     * Valide un token et vérifie s'il est encore valide et authentique.
     *
     * @param string $token Le token à vérifier
     *
     * @return bool True si le token est valide sinon false
     */
    public function validateExpiringToken(string $token): bool
    {

        $decoded = base64_decode($token);
        if ($decoded === false) {
            return false;
        }

        // Sépare le token en ses composants (userId, expiration, signature)
        [$userId, $expiration, $signature] = explode('|', $decoded);

        // Vérifie si le token a expiré
        if ((int)$expiration < time()) {
            return false;
        }
        // Regénère la signature pour comparer avec celle du token
        $data = $userId . '|' . $expiration;
        $expectedSignature = hash_hmac('sha256', $data, $this->secretKey);

        // Vérifie si la signature correspond, empêchant ainsi les falsifications
        return hash_equals($signature, $expectedSignature);
    }

    // Décodage du token pour extraire les données (userId, expiration)
    public function decodeExpiringToken($token): array
    {
        $decoded = base64_decode($token);
        [$userId, $expiration] = explode('|', $decoded);  // On ne prend plus la signature ici

        return [
            'user_id' => (int)$userId, // ID de l'utilisateur
            'expiration' => (int)$expiration, // Timestamp d'expiration
        ];
    }

}