<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'app_user_profile', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Eager load all related data to avoid N+1 queries
        $user = $entityManager->createQueryBuilder()
            ->select('u', 'e', 'w', 's', 'cv')
            ->from(User::class, 'u')
            ->leftJoin('u.educations', 'e')
            ->leftJoin('u.workExperiences', 'w')
            ->leftJoin('u.skills', 's')
            ->leftJoin('u.cvUpload', 'cv')
            ->where('u.id = :id')
            ->setParameter('id', $currentUser->getId())
            ->getQuery()
            ->getSingleResult();

        // Pre-process skills by category to avoid complex Twig logic
        $skillsByCategory = [];
        foreach ($user->getSkills() as $skill) {
            $category = $skill->getCategory();
            if (!isset($skillsByCategory[$category])) {
                $skillsByCategory[$category] = [];
            }
            $skillsByCategory[$category][] = $skill;
        }

        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
            'isOwnProfile' => true,
            'skillsByCategory' => $skillsByCategory,
        ]);
    }

    #[Route('/view/{id}', name: 'app_user_profile_view', methods: ['GET'])]
    public function viewProfile(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Only admins can view other users' profiles
        if (!$currentUser->isAdmin() && $currentUser->getId() !== $id) {
            throw $this->createAccessDeniedException('You can only view your own profile.');
        }

        // Eager load all related data to avoid N+1 queries
        $profileUser = $entityManager->createQueryBuilder()
            ->select('u', 'e', 'w', 's', 'cv')
            ->from(User::class, 'u')
            ->leftJoin('u.educations', 'e')
            ->leftJoin('u.workExperiences', 'w')
            ->leftJoin('u.skills', 's')
            ->leftJoin('u.cvUpload', 'cv')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$profileUser) {
            throw $this->createNotFoundException('User not found.');
        }

        // Pre-process skills by category to avoid complex Twig logic
        $skillsByCategory = [];
        foreach ($profileUser->getSkills() as $skill) {
            $category = $skill->getCategory();
            if (!isset($skillsByCategory[$category])) {
                $skillsByCategory[$category] = [];
            }
            $skillsByCategory[$category][] = $skill;
        }

        return $this->render('user_profile/index.html.twig', [
            'user' => $profileUser,
            'isOwnProfile' => $currentUser->getId() === $profileUser->getId(),
            'skillsByCategory' => $skillsByCategory,
        ]);
    }

    #[Route('/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            
            // Verify current password
            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Current password is incorrect.');
                return $this->render('user_profile/change_password.html.twig', [
                    'changePasswordForm' => $form,
                ]);
            }

            // Hash and set new password
            $newPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Your password has been changed successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/change_password.html.twig', [
            'changePasswordForm' => $form,
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
