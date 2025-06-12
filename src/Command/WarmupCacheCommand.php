<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserProfileCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:warmup-cache',
    description: 'Warm up application caches for better performance'
)]
class WarmupCacheCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserProfileCacheService $cacheService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserProfileCacheService $cacheService
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->cacheService = $cacheService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Warming up application caches');

        // Warm up user profile completion cache
        $io->section('Warming up user profile completion cache');
        
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $io->progressStart(count($users));

        foreach ($users as $user) {
            $this->cacheService->getProfileCompletionPercentage($user);
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success(sprintf('Warmed up profile completion cache for %d users', count($users)));

        // Clear and warm up Doctrine metadata cache
        $io->section('Warming up Doctrine metadata cache');
        
        try {
            $metadataFactory = $this->entityManager->getMetadataFactory();
            $metadataFactory->getAllMetadata();
            $io->success('Doctrine metadata cache warmed up');
        } catch (\Exception $e) {
            $io->warning('Could not warm up Doctrine metadata cache: ' . $e->getMessage());
        }

        // Warm up commonly used queries
        $io->section('Warming up common queries');
        
        try {
            // Warm up task queries
            $taskRepo = $this->entityManager->getRepository(\App\Entity\Task::class);
            $taskRepo->findAllWithUsers(10); // Load first 10 tasks
            
            // Warm up user queries with relations
            $userRepo = $this->entityManager->getRepository(User::class);
            $userRepo->findAllWithRelations(10); // Load first 10 users with relations
            
            $io->success('Common queries warmed up');
        } catch (\Exception $e) {
            $io->warning('Could not warm up common queries: ' . $e->getMessage());
        }

        $io->success('Cache warmup completed successfully!');

        return Command::SUCCESS;
    }
}
