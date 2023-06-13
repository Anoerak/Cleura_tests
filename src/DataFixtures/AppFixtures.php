<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Forum;

use App\Entity\Video;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // we create 2 users, one admin and one regular user.
        $admin = new User();
        $admin->setEmail('admin@admin.se')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encoder->hashPassword($admin, 'Cleur@2023'))
            ->setName('admin')
            ->setIsVerified(true);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@user.se')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->encoder->hashPassword($user, 'Cleur@2023'))
            ->setName('user')
            ->setIsVerified(true);
        $manager->persist($user);

        // We create one Forum named 'General' and one named 'Feedback'.
        $forum1 = new Forum();
        $forum1->setName('General');
        $manager->persist($forum1);

        $forum2 = new Forum();
        $forum2->setName('Feedback');
        $manager->persist($forum2);


        // We create 3 posts all attributed to the admin.
        $post1 = new Post();
        $post1->setForum($forum1)
            ->setAuthor($admin)
            ->setMessage('Hi! This is the first post in the General Forum. It was created by the Admin.')
            ->setCreatedAt(new \DateTimeImmutable('2020-12-17 15:20:42'))
            ->setTitle('First Post');
        $manager->persist($post1);

        $post2 = new Post();
        $post2->setForum($forum1)
            ->setAuthor($admin)
            ->setMessage('Hi! This is the second post in the General Forum. It was also created by the Admin.')
            ->setCreatedAt(new \DateTimeImmutable('2020-12-17 15:21:29'))
            ->setTitle('Second Post');
        $manager->persist($post2);

        $post3 = new Post();
        $post3->setForum($forum2)
            ->setAuthor($admin)
            ->setMessage('Hi! This is the first post in the Feedback Forum.\nThis post was created by the AdminUser and it contains a newline.')
            ->setCreatedAt(new \DateTimeImmutable('2020-12-17 15:23:16'))
            ->setTitle('Third Post');
        $manager->persist($post3);

        // We create a loop that will generate 3 random videos attributed randomly to either the admin or the user.
        for ($i = 0; $i < 3; $i++) {
            $video = new Video();
            $video->setForum($i % 2 === 0 ? $forum1 : $forum2)
                ->setPath('https://www.youtube.com/watch?v=QH2-TGUlwu4')
                ->setAuthor($i % 2 === 0 ? $admin : $user)
                ->setCreatedAt(new \DateTimeImmutable('2020-12-17 15:20:42'))
                ->setTitle('Video ' . ($i + 1));
            $manager->persist($video);
        }

        // We create a loop that will create a couple of comments for each post attributed randomly to either the admin or the user.
        for ($i = 0; $i < 10; $i++) {
            $comment = new Comment();
            $comment->setAuthor($i % 2 === 0 ? $admin : $user)
                ->setMessage('This is a comment created by ' . ($i % 2 === 0 ? 'the admin' : 'the user') . '.')
                ->setTarget($i % 2 === 0 ? $post1 : $post2)
                ->setCreatedAt(new \DateTimeImmutable('2020-12-17 15:20:42'));
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
