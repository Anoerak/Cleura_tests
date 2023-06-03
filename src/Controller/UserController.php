<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    #[Route('/users', name: 'app_users_list')]
    public function usersListAction(EntityManagerInterface $emi): Response
    {
        // Redirect to the login if not connected as ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to see the list of users!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/list.html.twig', [
            'controller_name' => 'UserController',
            'users' => $emi->getRepository(User::class)->findAll()
        ]);
    }



    #[Route('/users/create', name: 'app_user_create')]
    public function userCreateAction(EntityManagerInterface $emi, Request $request): Response
    {
        // Redirect to the login if not connected as ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to see the list of users!');
            return $this->redirectToRoute('app_login');
        }

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $emi->persist($user);
            $emi->flush();

            $this->addFlash('success', 'New user just added!');

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/users/{id}/edit', name: 'app_user_edit')]
    public function userEditAction(User $user, Request $request, EntityManagerInterface $emi): Response
    {
        // Redirect to the login if not connected as USER or ADMIN
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to edit a user!');
            return $this->redirectToRoute('app_login');
        }

        // If USER, deny access if not the owner of the post
        if ($this->isGranted('ROLE_USER') && $this->getUser() !== $user) {
            $this->addFlash('danger', 'You are not allowed to edit this user!');
            return $this->redirectToRoute('app_home');
        }

        // If the id is not found, redirect to the list of users
        if (!$user) {
            $this->addFlash('danger', 'User not found!');
            return $this->redirectToRoute('app_home');
        }

        // If user is ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(AdminType::class, $user);
        } else {
            $form = $this->createForm(UserType::class, $user);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('name')->getData())) {
                $user->setName($form->get('name')->getData());
            }

            if (!empty($form->get('email')->getData())) {
                $user->setEmail($form->get('email')->getData());
            }

            // if (!empty($form->get('password[')->getData()) && !empty($form->get('confirm_password')->getData())) {
            //     $password = $this->encoder->hashPassword($user, $user->getPassword());
            //     $user->setPassword($password);
            // }

            $emi->flush();

            $this->addFlash('success', 'Modifications saved!');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_users_list');
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete')]
    public function userDeleteAction(User $user, EntityManagerInterface $emi): Response
    {
        // Redirect to the login if not connected as ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to delete a user!');
            return $this->redirectToRoute('app_login');
        }

        // If the id is not found, redirect to the list of users
        if (!$user) {
            $this->addFlash('danger', 'User not found!');
            return $this->redirectToRoute('app_users_list');
        }

        $emi->remove($user);
        $emi->flush();

        $this->addFlash('success', 'The user has been deleted!');

        return $this->redirectToRoute('app_admin');
    }
}
