<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier');

        return $this->render('panier/index.html.twig', [
            'panier' => $panier
        ]);
    }
    #[Route('/ajoutPanier/{id}', name: 'app_ajout_panier')]
    public function ajoutPanier($id, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $panier = $session->get('panier');

        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        return $this->render('panier/index.html.twig', []);
    }
}
