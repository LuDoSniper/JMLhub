<?php

namespace App\Controller;

use App\Entity\Movie\Category;
use App\Form\Movie\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}

    #[Route('/category/admin/create', 'app_category_create')]
    public function categoryCreate(
        Request $request
    ): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/Category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/category/admin/update/{id}', 'app_category_update')]
    public function categoryUpdate(
        Category $category,
        Request $request
    ): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/Category/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/category/admin/remove/{id}', 'app_category_remove')]
    public function categoryRemove(
        Category $category,
    ): Response
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/category/list', 'app_category_list')]
    public function categoryList(): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->render('Page/Category/list.html.twig', [
            'categories' => $categories
        ]);
    }
}