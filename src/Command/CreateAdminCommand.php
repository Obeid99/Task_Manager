<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create a default admin user with username: admin, email: admin@jems.com, password: admin',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check if admin user already exists
        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => 'admin@jems.com']);

        if ($existingUser) {
            // Update existing admin user
            $existingUser->setRoles(['ROLE_ADMIN']);
            $existingUser->setFirstName('Admin');
            $existingUser->setLastName('User');
            $existingUser->setJobTitle('System Administrator');
            $hashedPassword = $this->passwordHasher->hashPassword($existingUser, 'admin');
            $existingUser->setPassword($hashedPassword);

            try {
                $this->entityManager->flush();
                $output->writeln(' Admin user updated successfully (email: admin@jems.com, password: admin)');
            } catch (\Exception) {
                // If there's a username conflict, try with a different username
                $existingUser->setUsername('admin_' . uniqid());
                $this->entityManager->flush();
                $output->writeln(' Admin user updated with a new username (email: admin@jems.com, password: admin)');
            }
        } else {
            // Create new admin user
            try {
                $user = new User();
                $user->setEmail('admin@jems.com');
                $user->setUsername('admin');
                $user->setFirstName('Admin');
                $user->setLastName('User');
                $user->setJobTitle('System Administrator');
                $user->setRoles(['ROLE_ADMIN']);

                $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin');
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $output->writeln(' Admin user created successfully (email: admin@jems.com, password: admin)');
            } catch (\Exception) {
                // If there's a username conflict, try with a different username
                $user = new User();
                $user->setEmail('admin@jems.com');
                $user->setUsername('admin_' . uniqid());
                $user->setFirstName('Admin');
                $user->setLastName('User');
                $user->setJobTitle('System Administrator');
                $user->setRoles(['ROLE_ADMIN']);

                $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin');
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $output->writeln(' Admin user created with a unique username (email: admin@jems.com, password: admin)');
            }
        }

        return Command::SUCCESS;
    }
}
