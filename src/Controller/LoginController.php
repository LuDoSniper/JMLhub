<?php

namespace App\Controller;

use App\Form\LoginFormType;
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
        return $this->render('Pages/login.html.twig');

    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

}
