<?php

namespace App\Controller;

use App\Form\LoginFormType;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response{

        $form = $this->createForm(LoginFormType::class);

        return $this->render('Pages/login.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

}
