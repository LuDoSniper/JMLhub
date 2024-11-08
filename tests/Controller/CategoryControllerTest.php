<?php

namespace tests\Controller;

use App\Entity\Authentication\User;
use App\Entity\Movie\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testCategoryCreate(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/category/admin/create');
        $form = $crawler->filter('form')->form([
            'category[name]' => 'Test Category',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/home');
        $this->client->followRedirect();

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);
        $this->assertNotNull($category);
        $this->assertSame('Test Category', $category->getName());
    }

    public function testCategoryUpdate(): void
    {
        $category = new Category();
        $category->setName('Initial Category');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/category/admin/update/' . $category->getId());
        $form = $crawler->filter('form')->form([
            'category[name]' => 'Updated Category',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/home');
        $this->client->followRedirect();

        $updatedCategory = $this->entityManager->getRepository(Category::class)->find($category->getId());
        $this->assertSame('Updated Category', $updatedCategory->getName());
    }

    public function testCategoryRemove(): void
    {
        $category = new Category();
        $category->setName('Category To Be Deleted');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/category/admin/remove/' . $category->getId());

        $this->assertResponseRedirects('/home');
        $this->client->followRedirect();

        $this->assertNull($category->getId());
    }

    public function testCategoryList(): void
    {
        $category1 = new Category();
        $category1->setName('Category One');
        $category2 = new Category();
        $category2->setName('Category Two');

        $this->entityManager->persist($category1);
        $this->entityManager->persist($category2);
        $this->entityManager->flush();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);

        $this->client->request('GET', '/category/list');
        $this->assertResponseIsSuccessful();

        $crawler = $this->client->getCrawler();
        $this->assertEquals('Category One', $crawler->filter('tbody tr')->eq(0)->filter('td')->text());
        $this->assertEquals('Category Two', $crawler->filter('tbody tr')->eq(1)->filter('td')->text());
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // Clear database entries to maintain test isolation
        $this->entityManager->createQuery('DELETE FROM App\Entity\Movie\Category')->execute();
        $this->entityManager->close();
    }
}