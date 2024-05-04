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




class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }


    #[Route('/commande', name: 'app_commande')]
    public function passerCommande(Request $request, SessionInterface $session): Response
    {

        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');
        $commande = new Commandes();
        $form = $this->createForm(CommandesType::class, $commande);
    
        $form->handleRequest($request);
     // Vérifier si l'utilisateur est connecté
     if (!$user instanceof Users) {

        // rediriger vers la page de connexion
        return $this->redirectToRoute('app_login');
    }
    else
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
                

           

    
            // Récupérer les enregistrements de panier associés à l'utilisateur
           // Récupérer les paniers associés à l'utilisateur
           $paniers = $entityManager->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);

    
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
    
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');
    
            // Rediriger l'utilisateur vers une autre page, par exemple la page d'accueil
            return $this->redirectToRoute('app_marketplace');
        }
    
        return $this->render('commande/passerCommande.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}
    
    
}