<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Participation;

class ParticipationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function countByEvenement(int $idEvenement): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.idParticipation)')
            ->andWhere('p.idEvenement = :idEvenement')
            ->setParameter('idEvenement', $idEvenement)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
