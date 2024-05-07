<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commandes;
use App\Form\CommandesType;
use App\Entity\Users;
use App\Entity\Panier;
use App\Entity\Produits;
use App\Form\ProduitType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Token;
use Stripe\Charge;





class CommandeController extends AbstractController
{


    private function calculerTotalPanier($user, EntityManagerInterface $entityManager)
    {
        // Récupérer les éléments du panier pour l'utilisateur connecté
        $panierItems = $entityManager->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);

        // Initialiser la variable pour stocker la somme des prix
        $totalPrix = 0;

        // Parcourir les éléments du panier et ajouter le prix de chaque produit au total
        foreach ($panierItems as $panierItem) {
            $produit = $entityManager->getRepository(Produits::class)->find($panierItem->getIdProduit());
            if ($produit) {
                $totalPrix += $produit->getPrixProduit();
            }
        }

        // Retourner le totalPrix
        return $totalPrix;
  
   }

   #[Route('/commande', name: 'app_commande')]
   public function passerCommande(Request $request, SessionInterface $session, MailerInterface $mailer): Response
   {
       $user = $session->get('user');
   
       $commande = new Commandes();
       $form = $this->createForm(CommandesType::class, $commande);
   
       $form->handleRequest($request);
   
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager = $this->getDoctrine()->getManager();
   
           // Récupérer l'utilisateur connecté depuis la session
           $user = $session->get('user');
   
           // Vérifier si l'utilisateur est connecté
           if (!$user instanceof Users) {
               // rediriger vers la page de connexion
               return $this->redirectToRoute('app_login');
           }
   
           // Récupérer le montant total de la commande
           $totalPrix = $this->calculerTotalPanier($user, $entityManager);


           // Récupérer les enregistrements de panier associés à l'utilisateur
           $paniers = $entityManager->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);
           $token = $request->request->get('stripeToken');
           
           foreach ($paniers as $panier) {
               // Créer une nouvelle commande pour chaque enregistrement de panier
               $commande = new Commandes();
               $commande->setNomClient($form->get('nomClient')->getData());
               $commande->setPrenomClient($form->get('prenomClient')->getData());
               $commande->setTelephone($form->get('telephone')->getData());
               $commande->setEMail($form->get('eMail')->getData());
               $commande->setAdresseLivraison($form->get('adresseLivraison')->getData());
               $commande->setIdPanier($panier);
               $entityManager->persist($commande);
                }
              
               
               if ($request->isMethod('POST')) {
                // Effectuer le paiement en ligne avec Stripe
                \Stripe\Stripe::setApiKey("sk_test_51P9BRURobhmtORDy23ZRl1AVlG1Due5RstcDP72dq4eOKRyMZzS9N4HBBX43RzANF4snGorsaJSpENCABhcJfS8a00ySPDKdu1");
                $charge = Charge::create([
                    'amount' => $totalPrix * 100, // Convertir le montant en centimes
                    'currency' => 'EUR',
                    'source' => $token,
                    'description' => 'Passer Commande'
                ]);

                $nombreProduits = count($paniers);
                
                // Envoi d'un e-mail au client
                $email = (new Email())
                ->from('shamsbensaid456@gmail.com')
                ->to($form->get('eMail')->getData()) // Récupère l'e-mail du formulaire
                ->subject('Confirmation de commande')
                ->html($this->renderView('commande/commandeEmail.html.twig', [
                    'nomClient' => $form->get('nomClient')->getData(),
                    'prenomClient' => $form->get('prenomClient')->getData(),
                    'adresseLivraison' => $form->get('adresseLivraison')->getData(),
                    'totalPrix' => $totalPrix, // Récupérez le montant total de la commande depuis votre fonction calculerTotalPanier
                    'nombreProduits' => $nombreProduits, 
                ]));

                $mailer->send($email);

            }

            
           
           $entityManager->flush();
   
           $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');
   
           // Rediriger l'utilisateur vers une autre page, par exemple la page d'accueil
           return $this->redirectToRoute('my_publications');
       }
   
       return $this->render('commande/passerCommande.html.twig', [
           'form' => $form->createView(),
           'user' => $user,
       ]);
   }
   
    
    // Méthode pour supprimer le contenu du panier
    private function supprimerContenuPanier(Users $user, EntityManagerInterface $entityManager): void
    {
        // Récupérer les enregistrements du panier associés à l'utilisateur
        $panierItems = $entityManager->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);

        // Supprimer tous les enregistrements du panier de l'utilisateur
        foreach ($panierItems as $panierItem) {
            $entityManager->remove($panierItem);
        }

        // Enregistrer les changements dans la base de données
        $entityManager->flush();
    }

}