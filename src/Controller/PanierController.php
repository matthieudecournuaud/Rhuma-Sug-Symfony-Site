<?php

namespace App\Controller;

use App\Services\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService): Response
    {
        $produits = $panierService->getProduitsPanier();

        return $this->render('panier/index.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/ajoutPanier/{id}', name: 'app_ajout_panier')]
    public function ajoutPanier($id, PanierService $panierService): Response
    {
        $panierService->ajouterProduit($id);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/validerPanier', name: 'app_valider_panier')]
    public function validerPanier()
    {
        return $this->render('panier/paiement.html.twig');
    }

    #[Route('/payer', name: 'app_payer')]
    public function payer(PanierService $panierService)
    {
        // TODO
        // enregistre le panier en bdd avec l'état 1
        $user = $this->getUser();
        $panierService->enregistrerCommande($user);
        return $this->redirectToRoute('app_main');
    }
}
