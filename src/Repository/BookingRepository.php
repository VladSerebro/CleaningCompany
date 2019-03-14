<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    /**
     * BookingRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return mixed
     */
    public function getPossibleBookings()
    {
        $now = new \DateTime('now');
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.date > :now')
            ->setParameter('now', $now)
            ->orderBy('b.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return Booking[]
     */
    public function findAllSorted()
    {
        return $this->findBy(array(), array('date' => 'DESC'));
    }


//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
