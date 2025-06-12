<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service to safely delete users and handle related data
 */
class UserDeletionService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Safely delete a user and handle all related data
     */
    public function deleteUser(User $user): bool
    {
        try {
            $this->entityManager->beginTransaction();

            $userId = $user->getId();
            $userEmail = $user->getEmail();

            $this->logger->info('Starting user deletion process', [
                'user_id' => $userId,
                'user_email' => $userEmail
            ]);

            // Step 1: Handle tasks - set assignedTo and createdBy to null where this user is referenced
            $this->handleUserTasks($user);

            // Step 2: Delete related profile data (handled by cascade in entities)
            // Education, WorkExperience, Skills, CvUpload will be deleted automatically
            // due to cascade: ['persist', 'remove'] and orphanRemoval: true

            // Step 3: Delete the user (this will trigger cascade deletions)
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->entityManager->commit();

            $this->logger->info('User deletion completed successfully', [
                'user_id' => $userId,
                'user_email' => $userEmail
            ]);

            return true;

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            
            $this->logger->error('User deletion failed', [
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \RuntimeException('Failed to delete user: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Handle tasks related to the user being deleted
     */
    private function handleUserTasks(User $user): void
    {
        // Update tasks where this user is assigned - set assignedTo to null
        $assignedTasksQuery = $this->entityManager->createQuery(
            'UPDATE App\Entity\Task t SET t.assignedTo = NULL WHERE t.assignedTo = :user'
        );
        $assignedTasksQuery->setParameter('user', $user);
        $assignedTasksUpdated = $assignedTasksQuery->execute();

        // Update tasks where this user is the creator - set createdBy to null
        $createdTasksQuery = $this->entityManager->createQuery(
            'UPDATE App\Entity\Task t SET t.createdBy = NULL WHERE t.createdBy = :user'
        );
        $createdTasksQuery->setParameter('user', $user);
        $createdTasksUpdated = $createdTasksQuery->execute();

        $this->logger->info('Updated tasks for user deletion', [
            'user_id' => $user->getId(),
            'assigned_tasks_updated' => $assignedTasksUpdated,
            'created_tasks_updated' => $createdTasksUpdated
        ]);
    }

    /**
     * Get deletion preview - what will be deleted
     */
    public function getDeletionPreview(User $user): array
    {
        $preview = [
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getFullName()
            ],
            'related_data' => [
                'educations' => $user->getEducations()->count(),
                'work_experiences' => $user->getWorkExperiences()->count(),
                'skills' => $user->getSkills()->count(),
                'cv_upload' => $user->getCvUpload() ? 1 : 0,
            ],
            'tasks' => []
        ];

        // Count tasks where user is assigned
        $assignedTasksCount = $this->entityManager->createQuery(
            'SELECT COUNT(t.id) FROM App\Entity\Task t WHERE t.assignedTo = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        // Count tasks where user is creator
        $createdTasksCount = $this->entityManager->createQuery(
            'SELECT COUNT(t.id) FROM App\Entity\Task t WHERE t.createdBy = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        $preview['tasks'] = [
            'assigned_to_user' => $assignedTasksCount,
            'created_by_user' => $createdTasksCount,
            'action' => 'These tasks will have their user references set to NULL (tasks will remain but be unassigned/orphaned)'
        ];

        return $preview;
    }

    /**
     * Check if user can be safely deleted
     */
    public function canDeleteUser(User $user): array
    {
        $result = [
            'can_delete' => true,
            'warnings' => [],
            'blockers' => []
        ];

        // Check if user is admin
        if ($user->isAdmin()) {
            $result['warnings'][] = 'This is an admin user. Deletion will remove admin privileges.';
        }

        // Check for tasks
        $assignedTasksCount = $this->entityManager->createQuery(
            'SELECT COUNT(t.id) FROM App\Entity\Task t WHERE t.assignedTo = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        $createdTasksCount = $this->entityManager->createQuery(
            'SELECT COUNT(t.id) FROM App\Entity\Task t WHERE t.createdBy = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        if ($assignedTasksCount > 0) {
            $result['warnings'][] = "User has {$assignedTasksCount} assigned task(s). These will become unassigned.";
        }

        if ($createdTasksCount > 0) {
            $result['warnings'][] = "User created {$createdTasksCount} task(s). These will become orphaned.";
        }

        // Check for profile data
        $profileDataCount = $user->getEducations()->count() + 
                           $user->getWorkExperiences()->count() + 
                           $user->getSkills()->count() + 
                           ($user->getCvUpload() ? 1 : 0);

        if ($profileDataCount > 0) {
            $result['warnings'][] = "User has profile data ({$profileDataCount} items) that will be permanently deleted.";
        }

        return $result;
    }
}
