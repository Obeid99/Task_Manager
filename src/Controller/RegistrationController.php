<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppCustomAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Redirect if user is already logged in
        if ($this->getUser()) {
            $this->addFlash('info', 'You are already registered and logged in.');
            return $this->redirectToRoute('app_task_index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Debug form errors
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }

                // Debug specific field errors
                if ($form->get('username')->getErrors()->count() > 0) {
                    foreach ($form->get('username')->getErrors() as $error) {
                        $this->addFlash('error', 'Username error: ' . $error->getMessage());
                    }
                }

                if ($form->get('email')->getErrors()->count() > 0) {
                    foreach ($form->get('email')->getErrors() as $error) {
                        $this->addFlash('error', 'Email error: ' . $error->getMessage());
                    }
                }

                if ($form->get('plainPassword')->getErrors()->count() > 0) {
                    foreach ($form->get('plainPassword')->getErrors() as $error) {
                        $this->addFlash('error', 'Password error: ' . $error->getMessage());
                    }
                }
            } else {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                try {
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Your account has been created successfully!');

                    // Automatically authenticate the user after registration
                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Registration failed: ' . $e->getMessage());
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
