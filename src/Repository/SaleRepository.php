<?php

namespace App\Repository;

use App\Entity\Sale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sale>
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * Récupère les ventes avec le statut "en attente" uniquement
     * Gère les statuts invalides en base de données
     * 
     * @return Sale[]
     */
    public function findPendingSales(): array
    {
        // Utiliser une requête SQL brute pour récupérer les IDs des ventes "en attente"
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM sale WHERE status = 'en attente' ORDER BY created_at DESC";
        $ids = $conn->fetchFirstColumn($sql);
        
        if (empty($ids)) {
            return [];
        }
        
        // Charger les entités une par une en gérant les exceptions
        $sales = [];
        foreach ($ids as $id) {
            try {
                $sale = $this->find($id);
                if ($sale && $sale->getStatus() && $sale->getStatus()->value === 'en attente') {
                    $sales[] = $sale;
                }
            } catch (\Exception $e) {
                // Ignorer les ventes avec des statuts invalides
                continue;
            }
        }
        
        return $sales;
    }

    /**
     * Récupère toutes les ventes triées : "en attente" en premier, puis par date de création (plus récent en premier)
     * Toutes les ventes "en attente" (de tous les utilisateurs) s'affichent en premier
     * Gère les statuts invalides en base de données
     * 
     * @return Sale[]
     */
    public function findAllOrderedByStatus(): array
    {
        // Utiliser une requête SQL brute pour récupérer tous les IDs
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM sale ORDER BY created_at DESC";
        $ids = $conn->fetchFirstColumn($sql);
        
        if (empty($ids)) {
            return [];
        }
        
        // Charger les entités une par une en gérant les exceptions
        $allSales = [];
        foreach ($ids as $id) {
            try {
                $sale = $this->find($id);
                if ($sale && $sale->getStatus()) {
                    $allSales[] = $sale;
                }
            } catch (\Exception $e) {
                // Ignorer les ventes avec des statuts invalides
                continue;
            }
        }
        
        // Séparer les ventes "en attente" des autres
        $enAttente = [];
        $autres = [];
        
        foreach ($allSales as $sale) {
            try {
            if ($sale->getStatus() && $sale->getStatus()->value === 'en attente') {
                $enAttente[] = $sale;
            } else {
                $autres[] = $sale;
                }
            } catch (\Exception $e) {
                // Ignorer les ventes avec des statuts invalides
                continue;
            }
        }
        
        // Trier chaque groupe par date de création (plus récent en premier)
        usort($enAttente, function($a, $b) {
            $aDate = $a->getCreatedAt() ?? new \DateTime('1970-01-01');
            $bDate = $b->getCreatedAt() ?? new \DateTime('1970-01-01');
            return $bDate <=> $aDate;
        });
        
        usort($autres, function($a, $b) {
            $aDate = $a->getCreatedAt() ?? new \DateTime('1970-01-01');
            $bDate = $b->getCreatedAt() ?? new \DateTime('1970-01-01');
            return $bDate <=> $aDate;
        });
        
        // Combiner : "en attente" en premier, puis les autres
        return array_merge($enAttente, $autres);
    }

//    /**
//     * @return Sale[] Returns an array of Sale objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sale
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
