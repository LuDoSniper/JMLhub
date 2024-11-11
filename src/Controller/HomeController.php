<?php

namespace App\Controller;

use App\Entity\Movie\Category;
use App\Entity\Movie\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        // Récupérer le dernier film ajouté
        $lastMovie = $entityManager->getRepository(Movie::class)->findOneBy([], ['id' => 'DESC']);

        // Récupérer toutes les catégories avec leurs films associés
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('Page/home.html.twig', [
            'username'=> $this->getUser()->getUsername(),
            'lastMovie' => $lastMovie,
            'categories' => $categories,
        ]);
    }

}
