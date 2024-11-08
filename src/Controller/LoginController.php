<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response{

//        $form = $this->createForm(LoginFormType::class);
//        $form->handleRequest($_REQUEST);

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('Page/Authentication/login.html.twig');

    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

}
