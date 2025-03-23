<?php

namespace App\Service;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ProductsEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SearchService
{
    private ProductsRepository $servicesRepository;

    public function __construct(ProductsRepository $servicesRepository)
    {
        $this->servicesRepository = $servicesRepository;
    }

    /**
     * Effectue une recherche dans les services en fonction d'une requête.
     *
     * @param string|null $searchQuery La chaîne à rechercher.
     * @return array Un tableau de résultats correspondant à la recherche.
     */
    public function search(?string $searchQuery ): array
    {

        if (empty(trim($searchQuery))) {
            return [];
        }

        return $this->servicesRepository->findByQuery($searchQuery);
    }


    public function FiltreSearch(?int $DeliveryDay, ?float $PriceMin): array
    {
       // $qb = $this->createQueryBuilder('r');
        $qb = $this->servicesRepository->createQueryBuilder('r');

    
        if ($PriceMin !== null) {
            $qb->andWhere('r.PriceMin >= :PriceMin')
               ->setParameter('PriceMin', $PriceMin);
        }
    
        if ($DeliveryDay !== null) {
            $qb->andWhere('r.DeliveryDay <= :DeliveryDay')
               ->setParameter('DeliveryDay', $DeliveryDay);
        }
    
        return $qb->getQuery()->getResult();
    }

    
}
