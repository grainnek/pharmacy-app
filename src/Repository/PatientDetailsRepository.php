<?php

namespace App\Repository;

use App\Entity\PatientDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PatientDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PatientDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PatientDetails[]    findAll()
 * @method PatientDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatientDetails::class);
    }

    // /**
    //  * @return PatientDetails[] Returns an array of PatientDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PatientDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
