<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    /**
     * Search users by name, email, or username
     */
    public function searchUsers(string $search): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.firstName LIKE :search')
            ->orWhere('u.lastName LIKE :search')
            ->orWhere('u.email LIKE :search')
            ->orWhere('u.username LIKE :search')
            ->orWhere('u.jobTitle LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get users with profile completion statistics
     */
    public function getUsersWithStats(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.educations', 'e')
            ->leftJoin('u.workExperiences', 'w')
            ->leftJoin('u.skills', 's')
            ->leftJoin('u.cvUpload', 'cv')
            ->addSelect('COUNT(DISTINCT e.id) as educationCount')
            ->addSelect('COUNT(DISTINCT w.id) as experienceCount')
            ->addSelect('COUNT(DISTINCT s.id) as skillCount')
            ->addSelect('CASE WHEN cv.id IS NOT NULL THEN 1 ELSE 0 END as hasCv')
            ->groupBy('u.id')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all users with eager loading of related entities - optimized for performance
     */
    public function findAllWithRelations(int $limit = 100): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.educations', 'e')
            ->leftJoin('u.workExperiences', 'w')
            ->leftJoin('u.skills', 's')
            ->leftJoin('u.cvUpload', 'cv')
            ->addSelect('e', 'w', 's', 'cv')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find users by search term with eager loading - optimized
     */
    public function searchUsersWithRelations(string $search, int $limit = 50): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.educations', 'e')
            ->leftJoin('u.workExperiences', 'w')
            ->leftJoin('u.skills', 's')
            ->leftJoin('u.cvUpload', 'cv')
            ->addSelect('e', 'w', 's', 'cv')
            ->where('u.firstName LIKE :search')
            ->orWhere('u.lastName LIKE :search')
            ->orWhere('u.email LIKE :search')
            ->orWhere('u.username LIKE :search')
            ->orWhere('u.jobTitle LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
