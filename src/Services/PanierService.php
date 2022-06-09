<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierService
{
    private $requestStack;
    private $produitRepository;
    private $doctrine;

    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository, ManagerRegistry $doctrine)
    {
        $this->requestStack = $requestStack;
        $this->produitRepository = $produitRepository;
        $this->doctrine = $doctrine;
    }

    public function ajouterProduit(int $id)
    {
        $session = $this->requestStack->getSession(); // $_SESSION
        $panier = $session->get('panier'); // $_SESSION["panier]
        // si le produit est déjà dans le panier, on incrémente la quantité
        // id = 153
        if (isset($panier[$id])) { // $panier = [ 153 => 1] , $_SESSION["panier][153] ?
            $panier[$id]++; // $panier = [ 153 => 2], 
        } else { // si le produit n'est pas dans le panier, on l'ajoute avec la quantité 1
            $panier[$id] = 1; // $panier = [ 153 => 1], 
        }
        $session->set('panier', $panier); // $_SESSION["panier][153] = 1 ou $_SESSION["panier][153] = 2
    }

    public function getProduitsPanier()
    {
        $produits = [];
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier');
        if ($panier) {
            // $panier =[
            //     151 => 2,
            //     152 => 1
            // ];
            foreach ($panier as $id => $quantite) {
                $produit = $this->produitRepository->find($id);
                $produit->qtite = $quantite;
                $produits[] = $produit;
            }
        }

        return $produits;
    }

    public function enregistrerCommande($user)
    {
        // on récupère l'entity manager pour pouvoir faire des persist et des flush
        $manager = $this->doctrine->getManager();
        // on crée une Commande
        $commande = new Commande();
        $commande->setDate(new \DateTime());
        $commande->setEtat(1);
        $commande->setUser($user);

        // on récupère le panier en session
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier');
        // $panier =[
        //     151 => 2,
        //     152 => 1
        // ];
        // pour chaque élément du panier, on crée une CommandeProduit et on la relie à la commande créée précédemment ($commande)
        foreach ($panier as $id => $quantite) {
            $commandeProduit = new CommandeProduit();
            $commandeProduit->setCommande($commande);
            $produit = $this->produitRepository->find($id); // on récupère le produit correspondant à $id
            $commandeProduit->setProduit($produit);
            $commandeProduit->setPrixVente($produit->getPrix());
            $commandeProduit->setQuantite($quantite);
            $manager->persist($commandeProduit);
        }
        // on enregistre en bdd
        $manager->persist($commande);
        $manager->flush();

        $session->remove('panier'); // on efface le panier
    }
}
