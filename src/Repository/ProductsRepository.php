<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    //    /**
    //     * @return Products[] Returns an array of Products objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findSorted(string $sortBy = null, string $direction = 'ASC', ?int $categoryId = null): array
    {
        $qb = $this->createQueryBuilder('p');
        
        // Appliquer le filtre par catégorie si spécifié
        if ($categoryId !== null) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
        
        // Appliquer le tri selon le paramètre sortBy
        switch ($sortBy) {
            case 'price':
                $qb->orderBy('p.price', $direction);
                break;
            case 'date':
                // Puisque vous n'avez pas de champ 'createdAt', triez par 'id' pour refléter l'ordre d'ajout
                $qb->orderBy('p.id', $direction); // Utilisez 'id' à la place de 'createdAt'
                break;
            default:
                // Tri par défaut (id)
                $qb->orderBy('p.id', $direction);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    public function findByQuery($query): array
    {
        // Nettoyage et validation du paramètre $query
        $query = trim($query);
        if (strlen($query) > 100) {
            $query = substr($query, 0, 100);
        }
        $query = preg_replace('/[^a-zA-Z0-9\s]/', '', $query);
        $query = strtolower($query);
    
        // Utilisation sécurisée de QueryBuilder avec setParameter
        return $this->createQueryBuilder('n')
        ->leftJoin('n.category', 'category')
        ->where('LOWER(n.title) LIKE :q') // Correction ici
        ->orWhere('LOWER(n.description_0) LIKE :q') // Adapter aux champs existants
        ->orWhere('LOWER(category.Name) LIKE :q') 
        ->setParameter('q', '%' . $query . '%')
        ->orderBy('n.id', 'DESC') // Remplacer Created_at si ce champ n'existe pas
        ->getQuery()
        ->getResult();
    }
}
