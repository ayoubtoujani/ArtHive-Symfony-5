<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    //  
    public function findAllProduitsWithUser()
    {
        return $this->createQueryBuilder('p')
        ->select('p', 'u.nomUser', 'u.prenomUser', 'u.photo', 'p.urlFile') // Include the contenuPublication attribute
        ->leftJoin('p.user', 'u') // Perform a join with the User entity
        ->getQuery()
        ->getResult();
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    // Add custom queries or methods here if needed
}
