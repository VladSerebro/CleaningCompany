<?php

namespace App\Repository;

use App\Entity\Cleaner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cleaner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cleaner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cleaner[]    findAll()
 * @method Cleaner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CleanerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cleaner::class);
    }

//    /**
//     * @return Cleaner[] Returns an array of Cleaner objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cleaner
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
