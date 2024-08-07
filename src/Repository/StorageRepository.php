<?php

namespace App\Repository;

use App\Entity\Storage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Storage>
 */
class StorageRepository extends ServiceEntityRepository
{
    const int LIMIT = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Storage::class);
    }

    public function findByPage(int $page): Paginator
    {
        $firstResult = ($page - 1) * self::LIMIT;

        $query = $this->createQueryBuilder('storage')
            ->leftJoin('storage.products', 'product')
            ->orderBy('storage.id', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults(self::LIMIT)
            ->getQuery();

        return new Paginator($query, true);
    }
}
