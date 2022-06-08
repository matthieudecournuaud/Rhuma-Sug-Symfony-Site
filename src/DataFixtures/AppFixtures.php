<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\CommandeProduit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->encoder = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {

        //--------------------------------------------- METHODE 1 ------------------------------------------------

        // $categorie = new Categorie();
        // $categorie->setNom('Rhum');

        // $categorie2 = new Categorie();
        // $categorie2->setNom('Sucre');

        // $manager->persist($categorie);
        // $manager->persist($categorie2);
        //--------------------------------------------- METHODE 2 ------------------------------------------------
        // $categories = ['Sucre', 'Rhum'];
        $categories = [];

        for ($i = 1; $i <= 5; $i++) {
            $categorie = new Categorie();
            $categorie->setNom('categorie ' . $i);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }



        // $prod =['Dzama', 'Bibilio', 'Gwenta'];
        $produits = [];


        for ($j = 1; $j <= 20; $j++) {
            $produit = new Produit();
            $produit->setCategorie($categories[random_int(0, count($categories) - 1)]);
            $produit->setDescription('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Repellendus, unde aliquid iusto at placeat quos minus. Quas iste nobis totam, dolore eligendi!');
            $produit->setNom('Produit ' . $j);
            $produit->setImg('produit1.jpg');
            $produit->setPrix($j + ($j / 10));
            $produits[] = $produit;

            $manager->persist($produit);
        }

        $toto = new User();
        $toto->setNom('Toto');
        $toto->setEmail('toto@toto.fr');
        $hashedPassword = $this->encoder->hashPassword($toto, 'toto');
        $toto->setPassword($hashedPassword);
        $manager->persist($toto);

        $commande = new Commande();
        $commande->setDate(new \DateTime());
        $commande->setEtat(0);
        $commande->setUser($toto);
        $manager->persist($commande);

        $cp1 = new CommandeProduit();
        $cp1->setCommande($commande);
        $cp1->setProduit($produits[0]);
        $cp1->setPrixVente($produits[0]->getPrix());
        $cp1->setQuantite(2);
        $manager->persist($cp1);

        $cp2 = new CommandeProduit();
        $cp2->setCommande($commande);
        $cp2->setProduit($produits[1]);
        $cp2->setPrixVente($produits[1]->getPrix());
        $cp2->setQuantite(3);
        $manager->persist($cp2);

        $cp3 = new CommandeProduit();
        $cp3->setCommande($commande);
        $cp3->setProduit($produits[3]);
        $cp3->setPrixVente($produits[3]->getPrix());
        $cp3->setQuantite(4);
        $manager->persist($cp3);


        $manager->flush();
    }
}
