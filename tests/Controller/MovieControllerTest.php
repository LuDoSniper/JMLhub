<?php

namespace tests\Controller;

use App\Entity\Authentication\User;
use App\Entity\Movie\Movie;
use App\Entity\Movie\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group movie
 */
class MovieControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testMovieCreate(): void
    {
        // Récupérer l'utilisateur admin et se connecter
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        // Créer un fichier temporaire pour l'upload
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            '/home/omydoo/Vidéos/Captures vidéo/Capture vidéo du 2024-09-04 16-28-29.webm',
            'Capture vidéo du 2024-09-04 16-28-29.webm',
            'video/webm',
            null,
            true
        );

        // Faire une requête GET pour afficher le formulaire
        $crawler = $this->client->request('GET', '/movie/admin/create');

        // Soumettre le formulaire avec les données et le fichier
        $form = $crawler->filter('form')->form([
            'movie[title]' => 'Test Movie',
            'movie[description]' => 'Description of the test movie.',
            'movie[releaseDate]' => '2024-01-01',
            'movie[rating]' => 4.5,
            'movie[file]' => $file,  // Le champ du fichier
        ]);

        $this->client->submit($form);

        // Vérifier que la réponse est une redirection
//        $this->assertResponseRedirects('/home');
//        $this->client->followRedirect();

        // Vérifier que le film a été créé dans la base de données
        $movie = $this->entityManager->getRepository(Movie::class)->findOneBy(['title' => 'Test Movie']);
        $this->assertNotNull($movie);
        $this->assertSame('Test Movie', $movie->getTitle());
        $this->assertSame('Description of the test movie.', $movie->getDescription());
        $this->assertSame(4.5, $movie->getRating());

        // Vérifier que le file_path n'est pas nul
        $filePath = $movie->getFilePath();
        $this->assertNotNull($filePath);

        // Vérifier que le fichier existe dans le répertoire de stockage
        $storageDirectory = '/path/to/storage/directory/'; // Remplacez par le répertoire de stockage de vos fichiers
        $fullFilePath = $storageDirectory . $filePath;

        // Vérifier que le fichier existe réellement sur le disque
        $this->assertFileExists($fullFilePath);

        // Optionnel : vérifier le type MIME du fichier téléchargé
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $this->assertSame('image/jpeg', $finfo->file($fullFilePath)); // Vérifie si le fichier est bien une image JPEG
    }

    public function testMovieUpdate(): void
    {
        // Create a category and a movie
        $category = new Category();
        $category->setName('Initial Category');
        $this->entityManager->persist($category);

        $movie = new Movie();
        $movie->setTitle('Initial Movie');
        $movie->setDescription('Initial description');
        $movie->setReleaseDate(new \DateTime('2023-01-01'));
        $movie->setRating(3.5);
        $movie->setCategory($category);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        // Update the movie
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/movie/admin/update/' . $movie->getId());
        $form = $crawler->filter('form')->form([
            'movie[title]' => 'Updated Movie',
            'movie[description]' => 'Updated description for the movie.',
            'movie[rating]' => 4.5,
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/home');
        $this->client->followRedirect();

        $updatedMovie = $this->entityManager->getRepository(Movie::class)->find($movie->getId());
        $this->assertSame('Updated Movie', $updatedMovie->getTitle());
        $this->assertSame('Updated description for the movie.', $updatedMovie->getDescription());
        $this->assertSame(4.5, $updatedMovie->getRating());
    }

    public function testMovieRemove(): void
    {
        // Create a category and a movie
        $category = new Category();
        $category->setName('Category to Remove');
        $this->entityManager->persist($category);

        $movie = new Movie();
        $movie->setTitle('Movie to be Deleted');
        $movie->setDescription('This movie will be deleted');
        $movie->setReleaseDate(new \DateTime('2023-01-01'));
        $movie->setRating(4.0);
        $movie->setCategory($category);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        // Remove the movie
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/movie/admin/remove/' . $movie->getId());

        $this->assertResponseRedirects('/home');
        $this->client->followRedirect();

        $deletedMovie = $this->entityManager->getRepository(Movie::class)->find($movie->getId());
        $this->assertNull($deletedMovie);
    }

    public function testMovieList(): void
    {
        // Create categories and movies
        $category1 = new Category();
        $category1->setName('Category One');
        $this->entityManager->persist($category1);

        $category2 = new Category();
        $category2->setName('Category Two');
        $this->entityManager->persist($category2);

        $movie1 = new Movie();
        $movie1->setTitle('Movie One');
        $movie1->setDescription('Description of Movie One');
        $movie1->setReleaseDate(new \DateTime('2023-01-01'));
        $movie1->setRating(3.0);
        $movie1->setCategory($category1);
        $this->entityManager->persist($movie1);

        $movie2 = new Movie();
        $movie2->setTitle('Movie Two');
        $movie2->setDescription('Description of Movie Two');
        $movie2->setReleaseDate(new \DateTime('2023-02-01'));
        $movie2->setRating(4.0);
        $movie2->setCategory($category2);
        $this->entityManager->persist($movie2);

        $this->entityManager->flush();

        // List movies
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/movie/list');
        $this->assertResponseIsSuccessful();

        $crawler = $this->client->getCrawler();
        $this->assertEquals('Movie One', $crawler->filter('tbody tr')->eq(0)->filter('td')->text());
        $this->assertEquals('Movie Two', $crawler->filter('tbody tr')->eq(1)->filter('td')->text());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clear database entries to maintain test isolation
        $this->entityManager->createQuery('DELETE FROM App\Entity\Movie\Movie')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Movie\Category')->execute();
        $this->entityManager->close();
    }
}
