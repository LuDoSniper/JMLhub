<?php

namespace App\Controller;

use App\Entity\Movie\Movie;
use App\Entity\Movie\Playlist;
use App\Form\Movie\AddToPlaylistType;
use App\Form\Movie\MovieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}

    #[Route('/movies', 'app_movies')]
    public function movies(): Response
    {
        $movies = $this->entityManager->getRepository(Movie::class)->findAll();

        return $this->render('Page/Movie/movies.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/movie/admin/create', 'app_movie_create')]
    public function create(
        Request $request
    ): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $file_path = uniqid() . '.' . $file->guessExtension();
            $file->move('movies', $file_path);
            $movie->setFilePath($file_path);

            $this->entityManager->persist($movie);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('Page/Movie/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/movie/admin/update/{id}', 'app_movie_update')]
    public function update(
        int $id,
        Request $request
    ): Response
    {
        $movie = $this->entityManager->getRepository(Movie::class)->findOneBy(['id' => $id]);
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('Page/Movie/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/movie/admin/remove/{id}', 'app_movie_remove')]
    public function remove(
        int $id,
        Request $request
    ): Response
    {
        $movie = $this->entityManager->getRepository(Movie::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($movie);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_movies');
    }

    #[Route('/movie/show/{id}', 'app_movie_show')]
    public function show(
        Movie $movie
    ): Response
    {
        return $this->render('Page/Movie/show.html.twig', [
            'movie' => $movie
        ]);
    }

    #[Route('/movie/{id}/add-to-playlist', name: 'app_movie_add_to_playlist')]
    public function addToPlaylist(Request $request, Movie $movie): Response
    {
        $user = $this->getUser();

        $playlists = $user ? $user->getPlaylists() : [];

        $form = $this->createForm(AddToPlaylistType::class, null, [
            'playlists' => $playlists,
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedPlaylists = $form->get('playlists')->getData();

            foreach ($selectedPlaylists as $playlist) {
                if ($playlist->getUser() === $user) {
                    $playlist->addMovie($movie);
                    $this->entityManager->persist($playlist);
                }
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('app_movies');
        }

        return $this->render('Page/Movie/add_to_playlist.html.twig', [
            'form' => $form->createView(),
            'playlists' => $playlists,
            'movie' => $movie,
        ]);
    }






}