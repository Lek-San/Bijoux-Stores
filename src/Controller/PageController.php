<?php

namespace App\Controller;
// App = src

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Lorsqu'on utilise une class, il faut l'importer ( clique droit import class / CTRL ALT I) ==> ligne use

class PageController extends AbstractController
{

    // * @Route est une annotation, elle s'écrit en commentaire mais est "lue" car elle commence un @
    // * 
    // * Il y a 2 arguments :
    // * le 1er est LA ROUTE => ce qu'on retrouve dans l'url du navigateur
    // * le 2e : le name de la route.
    // * 
    // * les valeurs de route et du name de la route doivent être en DOUBLE QUOTE
    // * 
    // * 
    // * On définit au dessus d'une fonction son annotation @Route
    // * ça veut dire que lorsqu'on sera sur le navigateur sur cette route dans l'URL, on rentrera dans la fonction
    // * on appliquera tout ce qu'il y a dedans




    /**
     * La fonction page() affiche les informations d'une page
     * 
     * @Route("/page", name="pageName")
     * 
     */
    public function PageFunction(): Response
    {

        $prenom = "Louis";



        return $this->render('page/page.html.twig', [
            "prenomTwig" => $prenom
            // nom qu'on récupère en twig => sa valeur dans le controller
        ]);
        // la méthode render() permet de définir la vue de la route
        // 1e argument obligatoire : le fichier html.twig 
        // 2e argument (non obligatoire) : c'est un tableau []
    }


    /**
     * la fonction home() est la route de la page principale du site cad : localhost:8000 ou www.nomdedomaine.fr
     * 
     * @Route("/", name="home")
     */

     public function home()
     {

        return $this->render("page/home.html.twig");
     }




















} // fermeture de la class PageController : Rien après
