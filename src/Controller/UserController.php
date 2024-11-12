<?php

namespace App\Controller;

use App\Entity\Authentication\User;
use App\Entity\Movie\Playlist;
use App\Form\Authentication\ProfileType;
use App\Form\Authentication\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){}

    #[Route('/user/admin/create', 'app_user_create')]
    public function userCreate(
        Request $request,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du user
            $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            if ($form->get('isAdmin')->getData()) {
                $user->addRole('ROLE_ADMIN');
            }
            $this->entityManager->persist($user);

            // Création des playlists par défaut
            $playlist = new Playlist();
            $playlist->setUser($user);
            $playlist->setName("Favoris");
            $playlist->setNative(true);
            $this->entityManager->persist($playlist);

            $playlist = new Playlist();
            $playlist->setUser($user);
            $playlist->setNative(true);
            $playlist->setName("À regarder plus tard");
            $this->entityManager->persist($playlist);

            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/User/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/user/admin/update/{id}', 'app_user_update')]
    public function userUpdate(
        User $user,
        Request $request
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isAdmin = $form->get('isAdmin')->getData();

            if ($isAdmin) {
                $user->addRole('ROLE_ADMIN');
            } else {
                $user->removeRole('ROLE_ADMIN');
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/User/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/admin/remove/{id}', 'app_user_remove')]
    public function userRemove(
        User $user,
    ): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/profile/update/', 'app_profile_update')]
    public function profileUpdate(
        Request $request
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('Page/User/profileUpdate.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/user/admin/list', 'app_user_list')]
    public function userList(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('Page/User/list.html.twig', [
            'users' => $users
        ]);
    }

}