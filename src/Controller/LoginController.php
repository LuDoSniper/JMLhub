<?php

namespace App\Controller;

use App\Form\Authentication\LoginFormType;
use App\Form\Security\ResetPasswordRequestFormType;
use App\Repository\Authentication\UserRepository;
use App\Service\SendEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
        //$token,
        SendEmailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

//        if($form->isSubmitted() && $form->isValid()) {
//
//        }
        return $this->render('Page/Security/resetPasswordRequest.html.twig', [
                'requestPassForm' => $form->createView()
        ]);
    }
}
