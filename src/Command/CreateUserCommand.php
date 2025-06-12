<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user with specified email and password',
)]
class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('name', InputArgument::REQUIRED, 'The full name of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        // Check if user already exists
        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => $email]);

        if ($existingUser) {
            // Update existing user
            $existingUser->setName($name);
            $hashedPassword = $this->passwordHasher->hashPassword($existingUser, $password);
            $existingUser->setPassword($hashedPassword);

            try {
                $this->entityManager->flush();
                $output->writeln(" User updated successfully (name: {$name}, email: {$email})");
            } catch (\Exception) {
                $output->writeln(" Error updating user. Please try again.");
                return Command::FAILURE;
            }
        } else {
            // Create new user
            try {
                $user = new User();
                $user->setEmail($email);
                $user->setName($name);
                // Generate a username from the email (part before @)
                $username = explode('@', $email)[0];
                // Add a unique suffix to avoid conflicts
                $user->setUsername($username . '_' . substr(uniqid(), -6));
                $user->setRoles(['ROLE_USER']);

                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $output->writeln(" User created successfully (name: {$name}, email: {$email})");
            } catch (\Exception) {
                $output->writeln(" Error creating user. Please try again.");
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
