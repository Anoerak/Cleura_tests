<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;
use App\Form\PostType;
use App\Service\AccessControllerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private AccessControllerService $accessControllerService,
    ) {
    }

    #[Route('/post/{id}', name: 'app_post_show')]
    public function show(int $id): Response
    {
        // Redirect to the login if not connected
        if ($this->accessControllerService->IsConnected('You must be logged in to see the list of posts!')) {
            return $this->redirectToRoute('app_login');
        } else if ($this->accessControllerService->IdIsCorrect($id, Post::class, 'The post you want to see does not exist!')) {
            return $this->redirectToRoute('app_forum');
        };

        // We get the post, the author and the forum from the database
        $post = $this->entityManagerInterface->getRepository(Post::class)->find($id);
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
    public function new(Request $request, int $forumId): Response
    {
        // Redirect to the login if not connected
        if ($this->accessControllerService->IsConnected('You must be logged in to create a new post!')) {
            return $this->redirectToRoute('app_login');
        };

        // We get the forum from the database based on the id in the url
        $forum = $this->entityManagerInterface->getRepository(Forum::class)->find($forumId);

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

            $this->entityManagerInterface->persist($post);
            $this->entityManagerInterface->flush();

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
    public function edit(Request $request, int $id): Response
    {

        // We get the post from the database
        $post = $this->entityManagerInterface->getRepository(Post::class)->find($id);

        // If not connected as ADMIN or USER, $post is null or the post does not belong to the user, redirect to the login
        if ($this->accessControllerService->IsConnected('You must be logged in to edit a post!')) {
            return $this->redirectToRoute('app_login');
        } else if ($this->accessControllerService->IdIsCorrect($id, Post::class, 'The post you want to edit does not exist!')) {
            return $this->redirectToRoute('app_forum');
        } else if ($this->accessControllerService->EditAccessController($post, 'You can only edit your own posts!')) {
            return $this->redirectToRoute('app_post_show', ['id' => $id]);
        };

        // We prepare the form
        $form = $this->createForm(PostType::class, $post);

        // We get the post from the database
        $post = $this->entityManagerInterface->getRepository(Post::class)->find($id);

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

            $this->entityManagerInterface->persist($post);
            $this->entityManagerInterface->flush();

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
    public function delete(int $id): Response
    {
        // We get the post from the database
        $post = $this->entityManagerInterface->getRepository(Post::class)->find($id);

        // If not connected as ADMIN or USER, $post is null or the post does not belong to the user, redirect to the login
        if ($this->accessControllerService->IsConnected('You must be logged in to delete a post!')) {
            return $this->redirectToRoute('app_login');
        } else if ($this->accessControllerService->IdIsCorrect($id, Post::class, 'The post you want to delete does not exist!')) {
            return $this->redirectToRoute('app_forum');
        } else if ($this->accessControllerService->EditAccessController($post, 'You can only delete your own posts!')) {
            return $this->redirectToRoute('app_post_show', ['id' => $id]);
        };

        // We get the forum of the post
        $forum = $this->entityManagerInterface->getRepository(Post::class)->find($id)->getForum();

        $this->entityManagerInterface->remove($this->entityManagerInterface->getRepository(Post::class)->find($id));
        $this->entityManagerInterface->flush();

        $this->addFlash('success', 'Post deleted! A backup has been sent to the NSA. Just in case...');

        return $this->redirectToRoute('app_forum_show', ['id' => $forum->getId()]);
    }
}
