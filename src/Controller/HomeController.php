<?php

namespace App\Controller;

use App\Entity\Movie\Category;
use App\Entity\Movie\Movie;
use App\Entity\Movie\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        // Récupérer le dernier film ajouté
        $lastMovie = $this->entityManager->getRepository(Movie::class)->findOneBy([], ['id' => 'DESC']);

        // Récupérer toutes les catégories avec leurs films associés
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        // Récupérer les playlists du user
        $playlists = $this->entityManager->getRepository(Playlist::class)->findBy(['user' => $this->getUser()]);

        return $this->render('Page/home.html.twig', [
            'username'=> $this->getUser()->getUsername(),
            'lastMovie' => $lastMovie,
            'categories' => $categories,
            'playlists' => $playlists
        ]);
    }

}
