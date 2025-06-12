<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserProfileCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/users')]
class UserApiController extends AbstractController
{
    #[Route('/register', name: 'api_user_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        try {
            // Get JSON data from request
            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validate required fields
            $requiredFields = ['email', 'password', 'firstName', 'lastName'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Check if user already exists
            $existingUser = $entityManager->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);
            
            if ($existingUser) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'User with this email already exists'
                ], Response::HTTP_CONFLICT);
            }

            // Create new user
            $user = new User();
            $user->setEmail($data['email']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);

            // Generate username from email if not provided
            $username = $data['username'] ?? explode('@', $data['email'])[0];
            $user->setUsername($username);
            
            // Optional fields
            if (!empty($data['jobTitle'])) {
                $user->setJobTitle($data['jobTitle']);
            }
            
            if (!empty($data['linkedinUrl'])) {
                $user->setLinkedinUrl($data['linkedinUrl']);
            }

            // Set role (default to user, allow admin if specified)
            $roles = ['ROLE_USER'];
            if (!empty($data['role']) && $data['role'] === 'admin') {
                $roles[] = 'ROLE_ADMIN';
            }
            $user->setRoles($roles);

            // Hash password
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            // Validate user entity
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }

            // Save user
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'username' => $user->getUsername(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'fullName' => $user->getFullName(),
                    'jobTitle' => $user->getJobTitle(),
                    'linkedinUrl' => $user->getLinkedinUrl(),
                    'roles' => $user->getRoles(),
                    'isAdmin' => $user->isAdmin(),
                    'profileCompletionPercentage' => $user->getProfileCompletionPercentage()
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while creating the user',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/list', name: 'api_user_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager, UserProfileCacheService $cacheService): JsonResponse
    {
        try {
            // Use optimized query with eager loading to prevent N+1 queries
            $users = $entityManager->createQueryBuilder()
                ->select('u', 'e', 'w', 's', 'cv')
                ->from(User::class, 'u')
                ->leftJoin('u.educations', 'e')
                ->leftJoin('u.workExperiences', 'w')
                ->leftJoin('u.skills', 's')
                ->leftJoin('u.cvUpload', 'cv')
                ->orderBy('u.firstName', 'ASC')
                ->addOrderBy('u.lastName', 'ASC')
                ->setMaxResults(100) // Limit for performance
                ->getQuery()
                ->getResult();

            // Warm up cache for all users to improve performance
            $cacheService->warmUpProfileCompletionCache($users);

            $userData = [];
            foreach ($users as $user) {
                $userData[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'fullName' => $user->getFullName(),
                    'jobTitle' => $user->getJobTitle(),
                    'linkedinUrl' => $user->getLinkedinUrl(),
                    'roles' => $user->getRoles(),
                    'isAdmin' => $user->isAdmin(),
                    'profileCompletionPercentage' => $cacheService->getProfileCompletionPercentage($user)
                ];
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $userData,
                'count' => count($userData)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while retrieving users',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $user = $entityManager->getRepository(User::class)->find($id);
            
            if (!$user) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'fullName' => $user->getFullName(),
                    'jobTitle' => $user->getJobTitle(),
                    'linkedinUrl' => $user->getLinkedinUrl(),
                    'roles' => $user->getRoles(),
                    'isAdmin' => $user->isAdmin(),
                    'profileCompletionPercentage' => $user->getProfileCompletionPercentage()
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while retrieving the user',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
