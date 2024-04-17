<?php

namespace App\Repository;

use App\Entity\Reclamationgroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamationgroupe>
 *
 * @method Reclamationgroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamationgroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamationgroupe[]    findAll()
 * @method Reclamationgroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationgroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamationgroupe::class);
    }
    /**
     * Find reclamation(s) by group ID.
     *
     * @param int $groupId The ID of the group.
     * @return Reclamationgroupe[] Returns an array of Reclamationgroupe objects.
     */
    public function findByGroupId(int $groupId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Reclamationgroupe[] Returns an array of Reclamationgroupe objects
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

//    public function findOneBySomeField($value): ?Reclamationgroupe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
