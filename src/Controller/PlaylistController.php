<?php

namespace App\Controller;

use App\Entity\Movie\Movie;
use App\Entity\Movie\Playlist;
use App\Form\Movie\PlaylistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/movie/playlists', 'app_playlists')]
    public function index(): Response
    {
        $playlists = $this->entityManager->getRepository(Playlist::class)->findAll();
        return $this->render('Page/Playlist/playlist.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    #[Route('/movie/playlist/create', 'app_playlist_create')]
    public function create(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($playlist);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_playlists');
        }

        return $this->render('Page/Playlist/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
