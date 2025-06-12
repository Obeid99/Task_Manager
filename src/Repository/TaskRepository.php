<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Find all tasks with eager loading - optimized
     * @return Task[]
     */
    public function findAllWithUsers(int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->addSelect('assignedTo', 'createdBy')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find tasks by user with eager loading - optimized
     * @return Task[]
     */
    public function findByUserWithUsers($user, int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->addSelect('assignedTo', 'createdBy')
            ->where('t.createdBy = :user OR t.assignedTo = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find one task with eager loading of user relationships
     */
    public function findOneWithUsers(int $id): ?Task
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->addSelect('assignedTo', 'createdBy')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find tasks by status with pagination
     */
    public function findByStatusWithPagination(bool $finished, int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('t')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->addSelect('assignedTo', 'createdBy')
            ->where('t.finished = :finished')
            ->setParameter('finished', $finished)
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count tasks by status
     */
    public function countByStatus(bool $finished): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.finished = :finished')
            ->setParameter('finished', $finished)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
