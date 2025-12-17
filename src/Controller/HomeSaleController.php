<?php

namespace App\Controller;

use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeSaleController extends AbstractController
{
    #[Route('/', name: 'app_home_sale')]
    public function index(SaleRepository $saleRepository, Request $request): Response
    {
        // Récupérer toutes les ventes disponibles (en attente ou disponible)
        // Exclure les ventes vendues (VENDUE) et payées (PAYER)
        $allSales = $saleRepository->findAll();
        
        // Filtrer pour ne garder que les ventes disponibles
        $sales = array_filter($allSales, function($sale) {
            if (!$sale->getStatus()) {
                return false;
            }
            $status = $sale->getStatus()->value;
            // Exclure les ventes vendues, payées, annulées, refusées
            return !in_array($status, ['vendue', 'payer', 'annule', 'refuser', 'payement en cours']);
        });

        // Filtre par type si présent dans la requête
        $typeFilter = $request->query->get('type');
        if ($typeFilter) {
            $sales = array_filter($sales, function($sale) use ($typeFilter) {
                return $sale->getType() && $sale->getType()->value === $typeFilter;
            });
        }

        return $this->render('homeSale/index.html.twig', [
            'sales' => array_values($sales), // Réindexer le tableau
            'typeFilter' => $typeFilter,
        ]);
    }
}

