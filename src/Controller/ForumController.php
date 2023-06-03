<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;
use App\Form\ForumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(EntityManagerInterface $emi): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to see the list of forums!');
            return $this->redirectToRoute('app_login');
        }

        $forums = $emi->getRepository(Forum::class)
            ->findAll();

        return $this->render('forum/forum.html.twig', [
            'controller_name' => 'ForumController',
            'forums' => $forums,
        ]);
    }

    #[Route('/forum/{id}', name: 'app_forum_show')]
    public function show(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to see the list of forums!');
            return $this->redirectToRoute('app_login');
        }

        $forum = $emi->getRepository(Forum::class)->find($id);

        // We get all the posts for this specific forum
        $posts = $emi->getRepository(Post::class)
            ->findBy(['forum' => $forum]);

        // We get the author of each post
        foreach ($posts as $post) {
            $post->setAuthor($emi->getRepository(User::class)->find($post->getAuthor()));
        }

        return $this->render('forum/show.html.twig', [
            'controller_name' => 'ForumController',
            'forum' => $forum,
            'posts' => $posts,
        ]);
    }

    #[Route('/create/forum', name: 'app_forum_create')]
    public function create(EntityManagerInterface $emi, Request $request): Response
    {
        // Redirect to the login if not connected as ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to create a forum!');
            return $this->redirectToRoute('app_login');
        }

        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($forum);
            $emi->flush();

            $this->addFlash('success', 'New forum just added!');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('forum/create.html.twig', [
            'controller_name' => 'ForumController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/forum/{id}/edit', name: 'app_forum_edit')]
    public function edit(EntityManagerInterface $emi, Request $request, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or $id is not found
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to edit a forum!');
            return $this->redirectToRoute('app_login');
        } else if (!$emi->getRepository(Forum::class)->find($id)) {
            $this->addFlash('danger', 'The forum you want to edit does not exist!');
            return $this->redirectToRoute('app_forum');
        }

        $forum = $emi->getRepository(Forum::class)->find($id);
        $form = $this->createForm(ForumType::class, $forum);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($forum);
            $emi->flush();

            $this->addFlash('success', 'Forum edited! Trying to make the proofs disappear?');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('forum/edit.html.twig', [
            'controller_name' => 'ForumController',
            'forum' => $forum,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/forum/{id}/delete', name: 'app_forum_delete')]
    public function delete(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or $id is not found
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to delete a forum!');
            return $this->redirectToRoute('app_login');
        } else if (!$emi->getRepository(Forum::class)->find($id)) {
            $this->addFlash('danger', 'The forum you want to delete does not exist!');
            return $this->redirectToRoute('app_forum');
        }

        $forum = $emi->getRepository(Forum::class)->find($id);

        $emi->remove($forum);
        $emi->flush();

        $this->addFlash('success', 'Forum deleted! All posts related to this forum have been deleted as well!');

        return $this->redirectToRoute('app_forum');
    }
}
