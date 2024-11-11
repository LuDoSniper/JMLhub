<?php

namespace App\Controller;

use App\Entity\Authentication\User;
use App\Form\Authentication\LoginFormType;
use App\Form\Authentication\SignupFormType;
use App\Form\Security\ResetPasswordFormType;
use App\Form\Security\ResetPasswordRequestFormType;
use App\Repository\Authentication\UserRepository;
use App\Service\SendEmailService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class);

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/Authentication/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

    #[Route('/forgottenpass', name: 'app_forgottenpassword')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenService $tokenService,
        SendEmailService $mail
    ): Response {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(['email' => $form->get('email')->getData()]);

            if($user) {

                $token = $tokenService->generateExpiringToken($user->getId());

                // Générer l'URL vers la page de réinitialisation du mot de passe
                $url = $this->generateUrl(
                    'app_reset_password', // Route vers la page de réinitialisation
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                // Envoyer l'email avec le lien de réinitialisation
                $mail->send(
                    'no-reply@jmlhub.fr', // Adresse de l'expéditeur
                    $user->getEmail(), // Email de l'utilisateur
                    'Récupération de mot de passe sur le site JMLHub', // Sujet de l'email
                    'password_reset', // Template Twig pour l'email
                    compact('user', 'url') // Variables à transmettre au template Twig
                );

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Email envoyé avec succès');

                // Rediriger vers la page de connexion après l'envoi de l'email
                return $this->redirectToRoute('app_login');

            }
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('danger', 'Un problème est survenu lors de votre demande de mot de passe');

        return $this->render('Page/Security/resetPasswordRequest.html.twig', [
                'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/forgottenpass/{token}', name: 'app_reset_password')]
    public function resetPassword(
        $token,
        TokenService $tokenService,
        UserRepository $usersRepository,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        // On vérifie si le token est valide
        if ($tokenService->validateExpiringToken($token)) {
            // Le token est valide
            // On récupère les données (userId et expiration)
            $decodedData = $tokenService->decodeExpiringToken($token);
            $userId = $decodedData['user_id'];
            $expiration = $decodedData['expiration'];

            // On récupère le user
            $user = $usersRepository->find($userId);

            if ($user) {
                $form = $this->createForm(ResetPasswordFormType::class);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    // On met à jour le mot de passe de l'utilisateur
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $form->get('password')->getData())
                    );

                    // Sauvegarde dans la base de données
                    $entityManager->flush();

                    // Ajout d'un message flash et redirection
                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('app_login');
                }

                return $this->render('Page/Security/reset_password.html.twig', [
                    'passForm' => $form->createView()
                ]);
            }
        }

        // Si le token est invalide ou a expiré
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();

        $form = $this->createForm(SignupFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/Authentication/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
