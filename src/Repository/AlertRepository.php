<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 */
class AlertRepository extends ServiceEntityRepository
{
    const int LIMIT = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findByPage(int $page): Paginator
    {
        $firstResult = ($page - 1) * self::LIMIT;

        $query = $this->createQueryBuilder('alert')
            ->leftJoin('alert.products', 'product')
            ->orderBy('alert.createdAt', 'DESC')
            ->where('alert.finished = 0')
            ->setFirstResult($firstResult)
            ->setMaxResults(self::LIMIT)
            ->getQuery();

        return new Paginator($query, true);
    }
}
