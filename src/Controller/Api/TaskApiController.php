<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tasks')]
class TaskApiController extends AbstractController
{
    #[Route('', name: 'api_task_list', methods: ['GET'])]
    public function list(TaskRepository $taskRepository): JsonResponse
    {
        try {
            // Get all tasks with eager loading
            $tasks = $taskRepository->findAllWithUsers();
            
            $taskData = [];
            foreach ($tasks as $task) {
                $taskData[] = $this->serializeTask($task);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Tasks retrieved successfully',
                'data' => $taskData,
                'count' => count($taskData)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while retrieving tasks',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/create', name: 'api_task_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
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
            $requiredFields = ['title', 'description', 'createdByEmail'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Find creator user
            $createdBy = $entityManager->getRepository(User::class)
                ->findOneBy(['email' => $data['createdByEmail']]);
            
            if (!$createdBy) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Creator user not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Create new task
            $task = new Task();
            $task->setTitle($data['title']);
            $task->setDescription($data['description']);
            $task->setCreatedBy($createdBy);
            
            // Optional fields
            if (!empty($data['dueDate'])) {
                $dueDate = new \DateTimeImmutable($data['dueDate']);
                $task->setDueDate($dueDate);
            }
            
            if (isset($data['finished'])) {
                $task->setFinished((bool) $data['finished']);
            }

            // Handle assignee
            if (!empty($data['assigneeEmail'])) {
                $assignee = $entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $data['assigneeEmail']]);
                
                if ($assignee) {
                    $task->setAssignedTo($assignee);
                } else {
                    // Store as external assignee
                    $task->setAssigneeEmail($data['assigneeEmail']);
                    if (!empty($data['assigneeName'])) {
                        $task->setAssigneeName($data['assigneeName']);
                    }
                }
            }

            // Validate task entity
            $errors = $validator->validate($task);
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

            // Save task
            $entityManager->persist($task);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $this->serializeTask($task)
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while creating the task',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'api_task_show', methods: ['GET'])]
    public function show(int $id, TaskRepository $taskRepository): JsonResponse
    {
        try {
            $task = $taskRepository->findOneWithUsers($id);
            
            if (!$task) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Task not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Task retrieved successfully',
                'data' => $this->serializeTask($task)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while retrieving the task',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function serializeTask(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'finished' => $task->isFinished(),
            'dueDate' => $task->getDueDate()?->format('Y-m-d'),
            'createdAt' => $task->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $task->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'assigneeName' => $task->getAssigneeName(),
            'assigneeEmail' => $task->getAssigneeEmail(),
            'assignedTo' => $task->getAssignedTo() ? [
                'id' => $task->getAssignedTo()->getId(),
                'email' => $task->getAssignedTo()->getEmail(),
                'fullName' => $task->getAssignedTo()->getFullName()
            ] : null,
            'createdBy' => [
                'id' => $task->getCreatedBy()->getId(),
                'email' => $task->getCreatedBy()->getEmail(),
                'fullName' => $task->getCreatedBy()->getFullName()
            ]
        ];
    }
}
