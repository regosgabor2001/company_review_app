<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompanyStatistics(): array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.id AS companyId',
                'c.name AS companyName',
                'c.reviewSummary',
                'COUNT(r.id) AS reviewCount',
                'AVG(r.rating) AS averageRating'
            )
            ->join('c.review', 'r')
            ->groupBy('c.id')
            ->orderBy('averageRating', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllWithReviews(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.review', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Company[] Returns an array of Company objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Company
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
