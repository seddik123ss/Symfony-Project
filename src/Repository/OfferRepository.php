<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * Récupère toutes les offres d'une vente
     * 
     * @return Offer[]
     */
    public function findBySale(int $saleId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.sale = :saleId')
            ->setParameter('saleId', $saleId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les offres acceptées
     * 
     * @return Offer[]
     */
    public function findAccepted(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.clientAccepted = :clientAccepted')
            ->andWhere('o.vendorAccepted = :vendorAccepted')
            ->setParameter('clientAccepted', true)
            ->setParameter('vendorAccepted', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les offres en attente
     * 
     * @return Offer[]
     */
    public function findPending(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les offres d'un client par email
     * 
     * @return Offer[]
     */
    public function findByClientEmail(string $email): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.clientEmail = :email')
            ->setParameter('email', $email)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

