<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{

    #[Route('/post/{id}', name: 'app_post_show')]
    public function show(EntityManagerInterface $emi, int $id): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to see the list of posts!');
            return $this->redirectToRoute('app_login');
        }

        // We get the post, the author and the forum from the database
        $post = $emi->getRepository(Post::class)->find($id);
        $author = $post->getAuthor();
        $forumId = $post->getForum()->getId();


        return $this->render('post/show.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
            'author' => $author,
            'forumId' => $forumId
        ]);
    }

    #[Route('/post/new/{forumId}', name: 'app_post_new')]
    public function new(EntityManagerInterface $emi, Request $request, int $forumId): Response
    {
        // Redirect to the login if not connected as ADMIN or USER
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to create a new post!');
            return $this->redirectToRoute('app_login');
        }

        // We get the forum from the database based on the id in the url
        $forum = $emi->getRepository(Forum::class)->find($forumId);

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // We get the user from the database and set it as the author of the post
            $user = $this->getUser();
            $post->setAuthor($user);
            $post->setForum($forum);
            // We set the date of the post
            $post->setCreatedAt(new \DateTimeImmutable());

            $emi->persist($post);
            $emi->flush();

            $this->addFlash('success', 'New post just added! Wait... Who are you?');

            // We get the id of the post and redirect to the show page
            $id = $post->getId();
            return $this->redirectToRoute('app_post_show', ['id' => $id]);
        }

        return $this->render('post/new.html.twig', [
            'controller_name' => 'PostController',
            'form' => $form->createView(),
            'forum' => $forum,
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit')]
    public function edit(EntityManagerInterface $emi, Request $request, int $id): Response
    {

        // We get the post from the database
        $post = $emi->getRepository(Post::class)->find($id);

        // If not connected as ADMIN or USER, $post is null or the post does not belong to the user, redirect to the login
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to edit a post!');
            return $this->redirectToRoute('app_login');
        } else if (!$post) {
            $this->addFlash('danger', 'The post you want to edit does not exist!');
            return $this->redirectToRoute('app_home');
        } else if (!$this->isGranted('ROLE_ADMIN')) {
            if ($post->getAuthor() != $this->getUser()) {
                $this->addFlash('danger', 'You can only edit your own posts!');
                return $this->redirectToRoute('app_post');
            }
        }

        // We prepare the form
        $form = $this->createForm(PostType::class, $post);

        // We get the post from the database
        $post = $emi->getRepository(Post::class)->find($id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // We get the user from the database and set it as the author of the post
            $user = $this->getUser();
            $post->setAuthor($user);
            $post->setForum($post->getForum());
            // We keep the original date of the post
            $post->setCreatedAt($post->getCreatedAt());
            // We get the new message of the post
            $post->setMessage($post->getMessage());

            $emi->persist($post);
            $emi->flush();

            $this->addFlash('success', 'Modifications saved! We can\'t guarantee that the NSA won\'t read them though...');

            // We get the id of the post and redirect to the show page
            $id = $post->getId();
            return $this->redirectToRoute('app_post_show', ['id' => $id]);
        }

        return $this->render('post/edit.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete')]
    public function delete(EntityManagerInterface $emi, int $id): Response
    {
        // If not connected as ADMIN or USER, redirect to the login
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_USER')) {
            $this->addFlash('danger', 'You must be logged in to delete a post!');
            return $this->redirectToRoute('app_login');
        } else if (!$emi->getRepository(Post::class)->find($id)) {
            $this->addFlash('danger', 'The post you want to delete does not exist!');
            return $this->redirectToRoute('app_post');
        } else if (!$this->isGranted('ROLE_ADMIN')) {
            $post = $emi->getRepository(Post::class)->find($id);
            if ($post->getAuthor() != $this->getUser()) {
                $this->addFlash('danger', 'You can only delete your own posts!');
                return $this->redirectToRoute('app_post_show', ['id' => $id]);
            }
        }

        // We get the forum of the post
        $forum = $emi->getRepository(Post::class)->find($id)->getForum();

        $emi->remove($emi->getRepository(Post::class)->find($id));
        $emi->flush();

        $this->addFlash('success', 'Post deleted! A backup has been sent to the NSA. Just in case...');

        return $this->redirectToRoute('app_forum_show', ['id' => $forum->getId()]);
    }
}
