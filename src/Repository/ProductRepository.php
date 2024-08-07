<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    const int LIMIT = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByPage(int $page): Paginator
    {
        $firstResult = ($page - 1) * self::LIMIT;

        $query = $this->createQueryBuilder('product')
            ->addSelect('product.id, product.name, product.quantity, product.minQuantity')
            ->setFirstResult($firstResult)
            ->setMaxResults(self::LIMIT)
            ->getQuery();

        return new Paginator($query, true);
    }

}
