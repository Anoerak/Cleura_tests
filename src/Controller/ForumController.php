<?php

namespace App\Controller;

use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(EntityManagerInterface $emi): Response
    {
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
        $forum = $emi->getRepository(Forum::class)->find($id);

        return $this->render('forum/show.html.twig', [
            'controller_name' => 'ForumController',
            'forum' => $forum,
        ]);
    }
}
