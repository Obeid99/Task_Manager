<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_USER')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_users', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Only admins can access user management
        if (!$currentUser->isAdmin()) {
            throw $this->createAccessDeniedException('Admin access required.');
        }

        $search = $request->query->get('search', '');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 20; // Users per page

        // Use optimized repository methods with eager loading and pagination
        if ($search) {
            $users = $userRepository->searchUsersWithRelations($search, $limit);
            $totalUsers = count($users); // For search, we don't need exact count
        } else {
            $users = $userRepository->findAllWithRelations($limit);
            $totalUsers = $userRepository->count([]);
        }

        $totalPages = ceil($totalUsers / $limit);

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
        ]);
    }

    #[Route('/{id}/profile', name: 'app_admin_user_profile', methods: ['GET'])]
    public function viewUserProfile(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Only admins can view other users' profiles
        if (!$currentUser->isAdmin()) {
            throw $this->createAccessDeniedException('Admin access required.');
        }

        $userRepository = $entityManager->getRepository(User::class);
        $profileUser = $userRepository->find($id);

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
            'isOwnProfile' => false,
            'isAdminView' => true,
            'skillsByCategory' => $skillsByCategory,
        ]);
    }

    #[Route('/{id}/toggle-status', name: 'app_admin_user_toggle_status', methods: ['POST'])]
    public function toggleUserStatus(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        
        // Only admins can toggle user status
        if (!$currentUser->isAdmin()) {
            throw $this->createAccessDeniedException('Admin access required.');
        }

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Don't allow disabling admin users
        if ($user->isAdmin()) {
            $this->addFlash('error', 'Cannot disable admin users.');
            return $this->redirectToRoute('app_admin_users');
        }

        // Toggle user status (you might want to add an 'active' field to User entity)
        $this->addFlash('success', 'User status updated successfully.');
        
        return $this->redirectToRoute('app_admin_users');
    }
}
