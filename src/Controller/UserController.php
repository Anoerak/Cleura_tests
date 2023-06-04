<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AdminType;
use App\Form\EditUserType;
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

    #[Route('/user/create', name: 'app_user_create')]
    public function userCreateAction(EntityManagerInterface $emi, Request $request): Response
    {
        // Redirect to the login if not connected as ADMIN
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     $this->addFlash('danger', 'You must be logged in with ADMIN privileges to see the list of users!');
        //     return $this->redirectToRoute('app_login');
        // }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $emi->persist($user);
            $emi->flush();

            $this->addFlash('success', 'New user just added! Wait... Who are you?');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function userEditAction(User $user, Request $request, EntityManagerInterface $emi): Response
    {

        // Redirect to the login if not connected as USER or ADMIN, wrong $id or not the same user
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to edit a user!');
            return $this->redirectToRoute('app_login');
        } else if (!$user) {
            $this->addFlash('danger', 'User not found!');
            return $this->redirectToRoute('app_home');
        } else if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $user) {
            $this->addFlash('danger', 'You are not allowed to edit this user!');
            return $this->redirectToRoute('app_home');
        }

        // If the user is ADMIN, he can edit all the fields
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(AdminType::class, $user);
        } else {
            $form = $this->createForm(EditUserType::class, $user);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('name')->getData())) {
                $user->setName($form->get('name')->getData());
            }

            if (!empty($form->get('email')->getData())) {
                $user->setEmail($form->get('email')->getData());
            }

            $emi->persist($user);
            $emi->flush();

            if ($this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', 'We saved your modification(s)! At least we think we did...');
                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_user_delete')]
    public function userDeleteAction(User $user, EntityManagerInterface $emi): Response
    {
        // Redirect to the login if not connected as ADMIN or wrong $id
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to delete a user!');
            return $this->redirectToRoute('app_login');
        } else if (!$user) {
            $this->addFlash('danger', 'User not found!');
            return $this->redirectToRoute('app_users_list');
        }

        $emi->remove($user);
        $emi->flush();

        $this->addFlash('success', 'The user has been deleted! Maybe...');

        return $this->redirectToRoute('app_admin');
    }
}