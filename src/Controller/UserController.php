<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AdminType;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use App\Service\AccessControllerService;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelper;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    public function __construct(private UserPasswordHasherInterface $encoder, private EntityManagerInterface $entityManagerInterface, private AccessControllerService $accessControllerService)
    {
    }

    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $request->get('id')]);

        dump($request->get('id'));

        if (null === $user) {
            throw $this->createNotFoundException('User not found.');
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('danger', $e->getReason());

            return $this->redirectToRoute('app_login');
        }


        $user->setIsVerified(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Your email address has been verified!! Lucky you!');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/user/create', name: 'app_user_create')]
    public function userCreateAction(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer): Response
    {
        // Redirect to the login if not connected as ADMIN
        if ($this->accessControllerService->IsAdmin('You must be logged in with ADMIN privileges to create a new user!')) {
            return $this->redirectToRoute('app_login');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getdata();

            /*
            |--------------------------------------------
            | We check if there already is a user with this username
            |--------------------------------------------
            */
            $userExists = $this->entityManagerInterface->getRepository(User::class)->findOneBy(['name' => $user->getName()]);
            if ($userExists) {
                $this->addFlash('danger', 'This username is already taken');
                return $this->redirectToRoute('app_admin');
            }

            $password = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();

            /*
            |--------------------------------------------
            | We generate the confirmation URL.
            |--------------------------------------------
            */
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            /*
            |--------------------------------------------
            | We send an email with a confirmation link.
            |--------------------------------------------
            */

            $email = (new TemplatedEmail())
                ->from(new Address('largowick@gmail.com', 'Cleura Daemon'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('emails/confirmation_email.html.twig')
                ->context([
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
                ]);

            $mailer->send($email);


            /*
            |--------------------------------------------
            | We check if the user has been created.
            |--------------------------------------------
            */
            if ($user->getId()) {
                $this->addFlash('success', 'New user just added! Wait... Who are you?');
                return $this->redirectToRoute('app_admin');
            } else {
                $this->addFlash('danger', 'Something went wrong...again.. Please, try one more time.');
                return $this->redirectToRoute('app_admin');
            }
        }

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/user/{id}/edit', name: 'app_user_edit')]
    public function userEditAction(User $user, Request $request): Response
    {

        // Redirect to the login if not connected as USER or ADMIN, wrong $id or not the same user
        if (
            $this->accessControllerService->IsConnected('You must be logged in to edit a user!') ||
            $this->accessControllerService->IdIsCorrect($user->getId(), User::class, 'User not found!') ||
            ($this->accessControllerService->EditUserProfileController($user, 'You are not allowed to edit this user!') && $this->accessControllerService->IsAdmin('You must be logged in with ADMIN privileges to edit a user!'))
        ) {
            return $this->redirectToRoute('app_login');
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

            $this->entityManagerInterface->persist($user);
            $this->entityManagerInterface->flush();

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
    public function userDeleteAction(User $user): Response
    {
        // Redirect to the login if not connected as ADMIN or wrong $id
        if (
            $this->accessControllerService->IsConnected('You must be logged in to delete a user!') ||
            $this->accessControllerService->IdIsCorrect($user->getId(), User::class, 'User not found!') ||
            $this->accessControllerService->IsAdmin('You must be logged in with ADMIN privileges to delete a user!')
        ) {
            return $this->redirectToRoute('app_login');
        }

        $this->entityManagerInterface->remove($user);
        $this->entityManagerInterface->flush();

        $this->addFlash('success', 'The user has been deleted! Maybe...');

        return $this->redirectToRoute('app_admin');
    }
}
