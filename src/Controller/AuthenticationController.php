<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthenticationController extends AbstractController
{
    #[Route('/login', 'app_login')]
    public function login(): Response
    {
        return $this->render('Page/Authentication/login.html.twig');
    }
}