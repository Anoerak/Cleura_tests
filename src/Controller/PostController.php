<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post')]
    public function index(): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to see the list of posts!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_show')]
    public function show(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to see the list of posts!');
            return $this->redirectToRoute('app_login');
        }

        // We get the post from the database
        $post = $emi->getRepository(Post::class)->find($id);

        // We get the author of the post
        $author = $post->getAuthor();

        // We get the forum Id of the post
        $forumId = $post->getForum()->getId();


        return $this->render('post/show.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
            'author' => $author,
            'forumId' => $forumId
        ]);
    }

    #[Route('/posts/new', name: 'app_post_new')]
    public function new(): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to create a new post!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('post/new.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/posts/{id}/edit', name: 'app_post_edit')]
    public function edit(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to edit a post!');
            return $this->redirectToRoute('app_login');
        }

        // If USER and not ADMIN, check if the post belongs to the user
        if (!$this->isGranted('ROLE_ADMIN')) {
            $post = $emi->getRepository(Post::class)->find($id);
            if ($post->getUser() != $this->getUser()) {
                $this->addFlash('danger', 'You can only edit your own posts!');
                return $this->redirectToRoute('app_post');
            }
        }

        // If the id is not found, redirect to the post list
        if (!$emi->getRepository(Post::class)->find($id)) {
            $this->addFlash('danger', 'The post you want to edit does not exist!');
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/edit.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/posts/{id}/delete', name: 'app_post_delete')]
    public function delete(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'You must be logged in with ADMIN privileges to delete a post!');
            return $this->redirectToRoute('app_login');
        }

        // If the id is not found, redirect to the post list
        if (!$emi->getRepository(Post::class)->find($id)) {
            $this->addFlash('danger', 'The post you want to delete does not exist!');
            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/delete.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
