<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserDeletionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-user',
    description: 'Safely delete a user and all related data'
)]
class DeleteUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserDeletionService $deletionService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserDeletionService $deletionService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->deletionService = $deletionService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user to delete')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Show what will be deleted without actually deleting')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Skip confirmation prompt')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $preview = $input->getOption('preview');
        $force = $input->getOption('force');

        // Find the user
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        $io->title("User Deletion Tool");
        $io->section("User Information");
        
        $io->definitionList(
            ['ID' => $user->getId()],
            ['Email' => $user->getEmail()],
            ['Name' => $user->getFullName() ?: 'N/A'],
            ['Username' => $user->getUsername()],
            ['Job Title' => $user->getJobTitle() ?: 'N/A'],
            ['Admin' => $user->isAdmin() ? 'Yes' : 'No']
        );

        // Check if user can be deleted
        $canDelete = $this->deletionService->canDeleteUser($user);
        
        if (!$canDelete['can_delete']) {
            $io->error('User cannot be deleted:');
            foreach ($canDelete['blockers'] as $blocker) {
                $io->text("• {$blocker}");
            }
            return Command::FAILURE;
        }

        // Show warnings
        if (!empty($canDelete['warnings'])) {
            $io->warning('Deletion warnings:');
            foreach ($canDelete['warnings'] as $warning) {
                $io->text("• {$warning}");
            }
        }

        // Get deletion preview
        $preview_data = $this->deletionService->getDeletionPreview($user);
        
        $io->section("Deletion Preview");
        $io->text("The following data will be affected:");
        
        $io->definitionList(
            ['Profile Data'],
            ['  Educations' => $preview_data['related_data']['educations'] . ' (will be deleted)'],
            ['  Work Experiences' => $preview_data['related_data']['work_experiences'] . ' (will be deleted)'],
            ['  Skills' => $preview_data['related_data']['skills'] . ' (will be deleted)'],
            ['  CV Upload' => $preview_data['related_data']['cv_upload'] . ' (will be deleted)'],
            ['Tasks'],
            ['  Assigned to user' => $preview_data['tasks']['assigned_to_user'] . ' (will become unassigned)'],
            ['  Created by user' => $preview_data['tasks']['created_by_user'] . ' (will become orphaned)']
        );

        if ($preview) {
            $io->success('Preview completed. Use --force to actually delete the user.');
            return Command::SUCCESS;
        }

        // Confirmation
        if (!$force) {
            if (!$io->confirm('Are you sure you want to delete this user and all related data?', false)) {
                $io->info('User deletion cancelled.');
                return Command::SUCCESS;
            }
        }

        // Perform deletion
        try {
            $io->section("Deleting User");
            $io->progressStart(1);
            
            $this->deletionService->deleteUser($user);
            
            $io->progressFinish();
            $io->success("User '{$email}' has been successfully deleted.");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error("Failed to delete user: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
