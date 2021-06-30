<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{

    // ProduitController contient les routes catalogue et fiche_produit
    // ==> la vue pour le client



    /**
     * La fonction catalogue() permet d'afficher les produits sur la vue Client
     * 
     * @Route("/catalogue", name="catalogue")
     */
    public function catalogue(ProduitRepository $repoProduit): Response
    {
        // Lorsqu'on créé une entity (=table en bdd), est généré en même temps un repository de ce dernier
        // le repository est la class liée à son entity permet ainsi de faire des requêtes de sélection

        // $repoProduit est l'instance / l'objet de la class ProduitRepository

        // on créer par les méthodes getDoctrine suivi de getRepository l'instance
        //$repoProduit = $this->getDoctrine()->getRepository(Produit::class);

        // OU ALORS, on appelle dans les parenthèses de la fonction la class ProduitRepository suivi du nom choisi pour son instance
        // C'est ce qu'on appelle UNE DEPENDANCE


        // Dans cette class ProduitRepository, il existe des méthodes(= fonctions) mais également on peut y créer les siennes
        // ->findAll() ==> SELECT * FROM produit
        // récupération de toutes les données se situant dans la table produit


        $produits = $repoProduit->findAll();

        // le dump s'affiche dans la navbar : Symfony profiler (situé en bas d'écran) sous le symbole du cible 
        // die permet de tuer (stopper) la suite du code, l'affichage sera sur toute la page du navigateur
        //dump($produits);die;

        //dd($produits);



        return $this->render('produit/catalogue.html.twig' , [
            "produits" => $produits
        ]);
    }


    /**
     * La fonction fiche_produit() permet d'afficher un produit sur la vue Client par le biais de la route catalogue
     * 
     * @Route("/fiche_produit/{id}" , name="fiche_produit")
     * 
     * pour récupérer une valeur qu'on aura préalablement défini dans le second argument du path() on doit rappeler la key id entouré d'accolade
     */
    public function fiche_produit(Produit $produit)
    {                           // $id, ProduitRepository $repoProduit
        

        //dump($id);
        //$produit = $repoProduit->find($id);
        // ->find($arg = champ id)
        // SELECT * FROM produit WHERE id = **valeur id récupérée**


        dump($produit);



        return $this->render('produit/fiche_produit.html.twig' , [
            'produit' => $produit
        ]);
    }












}
