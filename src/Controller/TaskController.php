<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskForm;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        // Use optimized queries with eager loading to prevent N+1 queries
        $isAdmin = $user->isAdmin();
        if ($isAdmin) {
            // Admins see all tasks
            $tasks = $taskRepository->findAllWithUsers();
        } else {
            $tasks = $taskRepository->findByUserWithUsers($user);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isAdmin = $user->isAdmin();
        $task = new Task();
        $form = $this->createForm(TaskForm::class, $task, [
            'is_admin' => $isAdmin
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the creator of the task
            $task->setCreatedBy($user);

            // For non-admin users, automatically assign the task to themselves
            if (!$isAdmin) {
                $task->setAssignedTo($user);
                $task->setAssigneeName($user->getFullName());
                $task->setAssigneeEmail($user->getEmail());
            } else {
                // For admin users, handle the assignee information from the form
                $this->handleAssigneeInfo($task, $entityManager);
            }

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task created successfully!');
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(int $id, TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        // Eager load task with user relationships
        $task = $taskRepository->findOneWithUsers($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found.');
        }

        // Check if user can access this task (creator, assignee, or admin)
        $isAdmin = $user->isAdmin();
        $isCreator = $task->getCreatedBy() && $task->getCreatedBy() === $user;
        $isAssignee = $task->getAssignedTo() && $task->getAssignedTo() === $user;

        if (!$isAdmin && !$isCreator && !$isAssignee) {
            throw $this->createAccessDeniedException('You can only view tasks you created or are assigned to.');
        }

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Eager load task with user relationships
        $task = $taskRepository->findOneWithUsers($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found.');
        }

        // Check if user can edit this task (only admin or task creator can edit)
        $isAdmin = $user->isAdmin();
        $isCreator = $task->getCreatedBy() && $task->getCreatedBy() === $user;
        $isAssignee = $task->getAssignedTo() && $task->getAssignedTo() === $user;

        if (!$isAdmin && !$isCreator) {
            throw $this->createAccessDeniedException('You can only edit tasks you created. Assigned users can only mark tasks as completed.');
        }

        $form = $this->createForm(TaskForm::class, $task, [
            'is_admin' => $isAdmin
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the assignee information
            $this->handleAssigneeInfo($task, $entityManager);

            $entityManager->flush();

            $this->addFlash('success', 'Task updated successfully!');
            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Eager load task with user relationships
        $task = $taskRepository->findOneWithUsers($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found.');
        }

        // Check if user can delete this task (only admin or task creator)
        $isAdmin = $user->isAdmin();
        $isCreator = $task->getCreatedBy() && $task->getCreatedBy() === $user;

        if (!$isAdmin && !$isCreator) {
            throw $this->createAccessDeniedException('You can only delete tasks you created. Assigned users cannot delete tasks.');
        }

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Task deleted successfully!');
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'app_task_complete', methods: ['POST'])]
    public function complete(Request $request, int $id, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Eager load task with user relationships
        $task = $taskRepository->findOneWithUsers($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found.');
        }

        // Check if user can mark this task as complete (assignee, creator, or admin)
        $isAdmin = $user->isAdmin();
        $isCreator = $task->getCreatedBy() && $task->getCreatedBy() === $user;
        $isAssignee = $task->getAssignedTo() && $task->getAssignedTo() === $user;

        if (!$isAdmin && !$isCreator && !$isAssignee) {
            throw $this->createAccessDeniedException('You can only complete tasks you created or are assigned to.');
        }

        if ($this->isCsrfTokenValid('complete'.$task->getId(), $request->getPayload()->getString('_token'))) {
            $task->setFinished(!$task->isFinished()); // Toggle completion status
            $entityManager->flush();

            $status = $task->isFinished() ? 'completed' : 'reopened';
            $this->addFlash('success', "Task {$status} successfully!");
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Handle the assignee information for a task
     */
    private function handleAssigneeInfo(Task $task, EntityManagerInterface $entityManager): void
    {
        // If assignee email is provided, try to find the user and set assignedTo
        if ($task->getAssigneeEmail()) {
            $assigneeUser = $entityManager->getRepository(User::class)
                ->findOneBy(['email' => $task->getAssigneeEmail()]);

            if ($assigneeUser) {
                // Found a user with this email - set the relationship
                $task->setAssignedTo($assigneeUser);

                // Also set the name if not provided
                if (!$task->getAssigneeName() && $assigneeUser->getFullName()) {
                    $task->setAssigneeName($assigneeUser->getFullName());
                }
            } else {
                // No user found - clear the assignedTo relationship but keep the email/name for external assignment
                $task->setAssignedTo(null);
            }
        } else {
            // No email provided - clear the assignedTo relationship
            $task->setAssignedTo(null);
        }
    }
}
