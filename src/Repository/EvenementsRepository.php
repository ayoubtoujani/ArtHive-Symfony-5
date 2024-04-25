<?php

namespace App\Repository;

use App\Entity\Evenements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evenements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenements[]    findAll()
 * @method Evenements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenements::class);
    }
    public function findByCategory($categorie)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.categorieevenement = :categorie')
            ->setParameter('categorie', $categorie)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les événements de la semaine en cours.
     *
     * @return Evenements[] Returns an array of Evenements objects
     */
    public function findEventsThisWeek(): array
    {
        $startOfWeek = new \DateTime('monday this week');
        $endOfWeek = new \DateTime('sunday this week');

        return $this->createQueryBuilder('e')
            ->andWhere('e.dDebutEvenement BETWEEN :start AND :end')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->orderBy('e.dDebutEvenement', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
