<?php

namespace App\Repository;

use App\Entity\Publications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use App\Entity\Users;


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
        ->leftJoin('p.user', 'u')
            ->where('p.contenuPublication LIKE :term')
            ->orWhere('u.nomUser LIKE :term')
            ->orWhere('u.prenomUser LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }
    public function findLikedPublicationIdsByUser(Users $user)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.idPublication')
            ->leftJoin('p.reactions', 'r')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    
        $result = $query->getResult();
    
        // Flatten the array of arrays to a flat array of publication IDs
        $publicationIds = array_column($result, 'idPublication');
    
        return $publicationIds;
    }

     /**
     * Find favorite publications for a given user.
     *
     * @param int $userId The ID of the user for whom to fetch favorite publications
     * @return array|null
     */
    public function findFavoritePublicationsForUser(int $userId)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.favoriteUsers', 'fu')
            ->andWhere('fu.idUser = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function findLikedPublicationsByUser(Users $user)
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.reactions', 'r')
        ->andWhere('r.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}


   
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publications::class);
    }

    // Add custom queries or methods here if needed
}


    
    
