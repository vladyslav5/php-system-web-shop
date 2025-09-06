<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrdersWithFilters(
        int $page = 1,
        int $limit = 10,
        ?string $status = null,
        ?string $email = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ): array {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.id');

        if ($status) {
            $qb->andWhere('o.status = :status')
                ->setParameter('status', $status);
        }

        if ($email) {
            $qb->andWhere('o.customerEmail = :email')
                ->setParameter('email', $email);
        }

        if ($dateFrom !== null) {
            $qb->andWhere('o.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if ($dateTo !== null) {
            $qb->andWhere('o.createdAt <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $totalItems = $paginator->count();
        $lastPage = (int)ceil($totalItems / $limit);

        return [
            'data' => iterator_to_array($paginator, false),
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'limit' => $limit,
            'lastPage' => $lastPage,
        ];
    }


    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
