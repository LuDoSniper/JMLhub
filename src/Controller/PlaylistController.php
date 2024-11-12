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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PlaylistController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/movie/playlists', 'app_playlists')]
    public function index(): Response
    {
        $playlists = $this->entityManager->getRepository(Playlist::class)->findBy(['user' => $this->getUser()->getId()]);
        return $this->render('Page/Playlist/playlist.html.twig', [
            'playlists' => $playlists,
        ]);
    }

    #[Route('/movie/playlist/create', 'app_playlist_create')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $playlist = new Playlist();
        $playlist->setUser($user);

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


    #[Route('/movie/playlist/remove/{id}', 'app_playlist_remove')]
    public function remove(
        Playlist $playlist
    ): Response
    {
        $playlists = $this->entityManager->getRepository(Playlist::class)->findBy(['user' => $this->getUser()->getId()]);
        if (!in_array($playlist, $playlists) || $playlist->getNative()) {
            throw new AccessDeniedException();
        }

        $this->entityManager->remove($playlist);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_playlists');
    }

    #[Route('/movie/playlist/update/{id}', 'app_playlist_update')]
    public function update(
        Playlist $playlist,
        Request $request
    ): Response
    {
        $playlists = $this->entityManager->getRepository(Playlist::class)->findBy(['user' => $this->getUser()->getId()]);
        if (!in_array($playlist, $playlists) || $playlist->getNative()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_playlists');
        }

        return $this->render('Page/Playlist/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/movie/playlist/{id}/add-movie/{movieId}', 'app_playlist_add_movie')]
    public function addMovie(
        int $id,
        int $movieId
    ): Response
    {
        $playlist = $this->entityManager->getRepository(Playlist::class)->find($id);
        $movie = $this->entityManager->getRepository(Movie::class)->find($movieId);

        if ($playlist && $movie) {
            $playlist->addMovie($movie);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_playlists');
    }

    #[Route('/movie/playlist/{id}/remove-movie/{movieId}', 'app_playlist_remove_movie')]
    public function removeMovie(
        int $id,
        int $movieId
    ): Response
    {
        $playlist = $this->entityManager->getRepository(Playlist::class)->find($id);
        $movie = $this->entityManager->getRepository(Movie::class)->find($movieId);

        if ($playlist && $movie) {
            $playlist->removeMovie($movie);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_playlists');
    }
}
