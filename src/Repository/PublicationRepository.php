<?php

namespace App\Repository;

use App\Entity\Publications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publications[]    findAll()
 * @method Publications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    //  
  /*  public function findAllPublicationsWithUser()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'u.nomUser', 'u.prenomUser', 'u.photo', 'p.urlFile', 'p.contenuPublication', 'p.dCreationPublication', 'p.idPublication')
            ->leftJoin('p.user', 'u') // Perform a join with the User entity
            ->getQuery()
            ->getResult();
    }
    */

    public function searchPublicationsByTerm($searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->where('p.contenuPublication LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publications::class);
    }

    // Add custom queries or methods here if needed
}


    
    