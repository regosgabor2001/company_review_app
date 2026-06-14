<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Return a page of reviews filtered by company name and ordered by rating.
     *
     * @param int         $page   Current page number (1-based)
     * @param int         $limit  Maximum number of reviews per page
     * @param string|null $search Search term for company name
     * @param string      $sort   Sort direction: "asc" or "desc"
     *
     * @return Review[]
     */
    public function findPaginatedWithSearchAndSort(int $page, int $limit, ?string $search, string $sort)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.company', 'c')
            ->addSelect('c');

        if ($search) {
            // Filter reviews by company name using a LIKE search.
            $qb->andWhere('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // Only allow a valid sort direction, default to DESC.
        $order = in_array($sort, ['asc', 'desc']) ? strtoupper($sort) : 'DESC';

        $qb->orderBy('r.rating', $order);

        return $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count reviews matching the optional company search term.
     *
     * @param string|null $search Search term for company name
     *
     * @return int
     */
    public function countWithSearch(?string $search)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->leftJoin('r.company', 'c');

        if ($search) {
            $qb->andWhere('c.name LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return Review[] Returns an array of Review objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Review
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
