<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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
        return $this->render('user/list.html.twig', [
            'controller_name' => 'UserController',
            'users' => $emi->getRepository(User::class)->findAll()
        ]);
    }



    #[Route('/users/create', name: 'app_user_create')]
    public function userCreateAction(EntityManagerInterface $emi, Request $request): Response
    {
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
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('name')->getData())) {
                $user->setName($form->get('name')->getData());
            }

            if (!empty($form->get('email')->getData())) {
                $user->setEmail($form->get('email')->getData());
            }

            if (!empty($form->get('password')->getData()) && !empty($form->get('confirm_password')->getData())) {
                $password = $this->encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $emi->flush();

            $this->addFlash('success', 'Modifications saved!');

            return $this->redirectToRoute('app_users_list');
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
        $emi->remove($user);
        $emi->flush();

        $this->addFlash('success', 'The user has been deleted!');

        return $this->redirectToRoute('app_admin');
    }
}