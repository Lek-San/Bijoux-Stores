<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class AdminProduitController extends AbstractController
{
    // AdminProduitController contient les routes de la gestion des produits (CRUD - BACK OFFICE)
    // gestion_produit/ajouter   ====> C
    // gestion_produit/afficher  ====> R
    // gestion_produit/modifier  ====> U
    // gestion_produit/supprimer ====> D

    // gestion_produit/image/supprimer 



    /**
     * La fonction produit_ajouter() permet d'ajouter un produit (back office / gestion des produits)
     * 
     * @Route("/gestion_produit/ajouter", name="produit_ajouter")
     */
    public function produit_ajouter(Request $request, EntityManagerInterface $manager): Response
    {
        // pour ajouter un produit on a besoin de créer une nouvelle instance / un objet de la class Produit
        $produit = new Produit;
        dump($produit); // l'instance est bien vide

        // Pour créer un formulaire (une instance ) on utilise la méthode createForm() 
        // 2 arguments obligatoires :
        // 1e : class du formulaire (dossier Form)
        // 2e : instance de la class Produit
        // 3e : facultatif : tableau qui définira l'option
        $form = $this->createForm(ProduitType::class, $produit, array(
            "ajouter" => true
        ));
        // $form est une instance/un objet


        $form->handleRequest($request); // traitement du formulaire


        // si le formulaire a été soumit (cliqué sur le bouton) et validé (respect des inputs => constraints)
        // on rentre dans la condition du traitement du formulaire pour l'envoi en BDD
        if($form->isSubmitted() && $form->isValid())
        {
            //dump($request);
            //dump($produit->getPrix());
            dump($produit);// dans l'objet $produit, on retrouve les valeurs des inputs


            // $imageFile récupère un tableau de données concernant une image upload
            // S'il n'y a pas d'image uplodé, $imageFile est vide / null
            
            $imageFile = $form->get('image')->getData();


            //dd($imageFile);


            if($imageFile)// si $imageFile n'est pas vide, on rentre dans la condition pour le traitement de l'image
            {

                // 3 étapes pour le traitement

                // 1e : renommer le nom de l'image 

                    $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();

                    // date("YmdHis") : 20210505162352
                    // uniqid() fonction prédéfinie PHP : génère un identifiant unique 
                    // $imageFile->getClientOriginalName() : le nom original de l'image, exemple : bague1.jpg

                    //dd($nomImage);

                // 2e étape : envoyer l'image dans le dossier public/images/imagesUpload


                    try // on éxecute le code dans le try
                    {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $nomImage

                        );
                        // la méthode move() permet de déplacer un fichier (dans le projet)
                        // 2 arguments :
                        // 1e : le "placement" (dans quel dossier l'image va se déplacer)
                        // 2e : le nom de l'image


                        // pour le placement
                        // la méthode getParameter() permet d'aller chercher dans le fichier services.yaml situé dans le dossier config
                        // config/services.yaml 
                        // il prend 1 argument, un nom choisi 
                        // qui doit se trouver dans le fichier services.yaml dans la partie parameters (ligne 6)
                        // On doit y trouver le nom choisi avec pour affectation le placement dans le projet
                        // images_directory: '%kernel.project_dir%/public/images/imagesUpload'
                        // %kernel.project_dir% => bijouterieD (le projet où on est)
                    }
                    catch(FileException $e) // s'il y a une erreur, on récupére ici et on l'affiche 
                    {

                    }

                // 3e étape : envoyer le nom de l'image en bdd 

                $produit->setImage($nomImage);
             



            }
            // pas de else, s'il n'y a pas d'image uplodé on continue la lecture du code, l'instance $produit ne contiendra d'image











            // sauf qu'il n'y a pas la date et elle ne peut pas être null en bdd
            // il faut donc la mettre avant l'envoi sinon erreur ...
            $produit->setCreatedAt( new \DateTime('now'));

            dump($produit); // ici on observe que l'objet est rempli au minimum, l'image peut être null car en bdd c'est possible


            // MVC
            // Model View Controller

            // Modele :
            //  - Entity (ORM => BDD)
            //  - Repository (Requête SQL SELECT)
            //  - EntityManagerInterface (Requêtes SQL INSERT INTO / UPDATE / DELETE )

            //dd($produit);
            $manager->persist($produit); // persist() on définit ce qu'on veut envoyer : l'instance $produit (contenant les valeurs)
            $manager->flush(); // flush() exécute donc envoie en bdd

           // dd($produit); après flush l'instance $produit contient un ID

            // Notification : produit bien ajouté.
            // addFlash() permet de créer un flash, un message, qui est créé depuis un controller pour être véhiculer et afficher sur le twig
            // 2 arguments :
            // 1e : le nom du flash
            // 2e : le message

            $this->addFlash("produit" , "Le produit " . $produit->getNom() . " a bien été ajouté");

            // Après envoie en bdd, tout est ok, on redirige la page sur une autre route 
            // fonction redirectToRoute() 
            // 1e : le name de route
            // 2e : facultatif 
            return $this->redirectToRoute("produit_afficher");

        }

        

        // la class Request contient les superglobales




        return $this->render('admin_produit/produit_ajouter.html.twig' , [
            "formProduit" => $form->createView() // dans l'objet form on a eu une méthode qui permet de créer une vue / un affichage pour le navigateur
        ]);
    }


    /**
     * La fonction produit_afficher() permet d'afficher sous forme de tableau la liste de tous les produits (back office / gestion des produits)
     * 
     * @Route("/gestion_produit/afficher", name="produit_afficher")
     */
    public function produit_afficher(ProduitRepository $repoProduit)
    {

        $produits = $repoProduit->findAll();

        return $this->render('admin_produit/produit_afficher.html.twig' , [
            "produits" => $produits
        ]);
    }


    /** 
     * La fonction produit_modifier() permet de modifier un produit (une instance) déjà existant en base de données (back office / gestion des produits)
     * 
     * @Route("/gestion_produit/modifier/{id}", name="produit_modifier")
     */
    public function produit_modifier(Produit $produit, Request $request, EntityManagerInterface $manager )
    {

       dump($produit); // l'instance est bien vide
       // la différence entre ajouter et modifier un produit
       // l'objet $produit dans l'ajout est vide
       // alors que dans la modification il contient des valeurs


       $form = $this->createForm(ProduitType::class, $produit, array(
            "modifier" => true
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

           
            // problématique :
            // injecter des valeurs dans des inputs est possible grâce à l'attribut value=""
            // Cependant il y a une exception avec l'input de type="file" 
            // La seule façon pour lui de contenir qqch (image/musique/vidéo/pdf) c'est en changeant (upload)

            // Solution : créer 2 $builders dans ProduitType
            // ajouter : image (Entity)
            // modifier : imageFile (mapped => false : il ne vient pas de l'entity)


            //dd($produit);


            $imageFile = $form->get('imageFile')->getData();


            //dd($imageFile);


            if($imageFile)// si $imageFile n'est pas vide, on rentre dans la condition pour le traitement de l'image
            {

                // 3 étapes pour le traitement

                // 1e : renommer le nom de l'image 

                    $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();

                    // date("YmdHis") : 20210505162352
                    // uniqid() fonction prédéfinie PHP : génère un identifiant unique 
                    // $imageFile->getClientOriginalName() : le nom original de l'image, exemple : bague1.jpg

                    //dd($nomImage);

                // 2e étape : envoyer l'image dans le dossier public/images/imagesUpload


                    try // on éxecute le code dans le try
                    {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $nomImage

                        );
                        // la méthode move() permet de déplacer un fichier (dans le projet)
                        // 2 arguments :
                        // 1e : le "placement" (dans quel dossier l'image va se déplacer)
                        // 2e : le nom de l'image


                        // pour le placement
                        // la méthode getParameter() permet d'aller chercher dans le fichier services.yaml situé dans le dossier config
                        // config/services.yaml 
                        // il prend 1 argument, un nom choisi 
                        // qui doit se trouver dans le fichier services.yaml dans la partie parameters (ligne 6)
                        // On doit y trouver le nom choisi avec pour affectation le placement dans le projet
                        // images_directory: '%kernel.project_dir%/public/images/imagesUpload'
                        // %kernel.project_dir% => bijouterieD (le projet où on est)
                    }
                    catch(FileException $e) // s'il y a une erreur, on récupére ici et on l'affiche 
                    {

                    }


                // Etape Ecoute en bdd s'il y a une ancienne image ou non
                // Pour les images lors d'une modification de produit
                // Il y a 2 possibilités :
                // soit le champ image en bdd est null
                // soit il contient le nom de l'image actuelle (existante)
                

                // Si le champ image en bdd n'est null ça veut dire qu'il y a déjà une image et il faut donc la retirer dans le dossier imageUpload
                if(!empty($produit->getImage()))
                {
                    // fonction prédéfinie PHP unlink() permet de supprimer un fichier d'un dossier 

                    unlink($this->getParameter('images_directory') . '/' . $produit->getImage());

                    // %kernel.project_dir%/public/images/imagesUpload  /   20210505......jpg
                }

 

                // 3e étape : envoyer le nom de l'image en bdd 

                $produit->setImage($nomImage);
             



            }

            // Pour la modification, l'instance $produit a un id donc il ne va pas être créé une nouvelle ligne (un nouveau produit) en bdd, la modification se fera sur la ligne correspondant à l'id
            $manager->persist($produit);
            $manager->flush();

            $this->addFlash("produit" , "Le produit " . $produit->getNom() . " a bien été modifié");

            return $this->redirectToRoute("produit_afficher");



        }


        return $this->render('admin_produit/produit_modifier.html.twig', [
            "formProduit" => $form->createView(),
            "produit" => $produit
        ]);
    }




    /**
     * La fonction produit_supprimer() permet de supprimer un produit existant en bdd (back office / gestion des produits)
     * 
     * @Route("/gestion_produit/supprimer/{id}", name="produit_supprimer")
     */
    public function produit_supprimer(Produit $produit, EntityManagerInterface $manager)
    {

        // Si le produit a une image, on la supprime
        if(!empty($produit->getImage()))
        {
            // fonction prédéfinie PHP unlink() permet de supprimer un fichier d'un dossier 

            unlink($this->getParameter('images_directory') . '/' . $produit->getImage());

            // %kernel.project_dir%/public/images/imagesUpload  /   20210505......jpg
        }


        // en BDD, on supprimer le produit (la ligne)
        $manager->remove($produit);
        $manager->flush();

        $this->addFlash("produit" , "Le produit " . $produit->getNom() . " a bien été supprimé");


        return $this->redirectToRoute("produit_afficher");



    }
    




    /**
     * La fonction image_produit_supprimer() permet de supprimer l'image d'un produit, (bdd = null)
     * 
     * @Route("/gestion_produit/image/supprimer/{id}", name="image_produit_supprimer")
     */
    public function image_produit_supprimer(Produit $produit, EntityManagerInterface $manager)
    {
        unlink($this->getParameter('images_directory') . '/' . $produit->getImage());

        $produit->setImage(null);

        $manager->persist($produit);
        $manager->flush();

        $this->addFlash("produit" , "L'image du produit " . $produit->getNom() . " a bien été supprimée");

        return $this->redirectToRoute("produit_modifier", [
            'id' => $produit->getId()
        ]);

        // Cette fonction n'a pas de render(), effectivement pour une supprime on n'a pas un visuel sur le navigateur
        // C'est uniquement une fonctionnalité
        // Mais on doit redirige à la fin du traitement sur une route 
        // Comme render() et path(), redirectToRoute peut avoir un 2e argument (facultatif) qui est un tableau

    }








}
