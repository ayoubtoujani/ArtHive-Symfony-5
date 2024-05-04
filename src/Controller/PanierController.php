<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Produits;
use App\Entity\Users;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('/supprimer/{id}', name: 'supprimer_du_panier', methods: ['POST'])]
    public function supprimerDuPanier(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le produit à supprimer du panier
        $panierItem = $entityManager->getRepository(Panier::class)->findOneBy(['idUser' => $user->getIdUser(), 'idProduit' => $id]);

        // Si le produit n'est pas trouvé dans le panier, gérer cela en conséquence
        if (!$panierItem) {
            // Par exemple, rediriger ou afficher un message d'erreur
            return $this->redirectToRoute('achat_produit'); // Rediriger vers la page du panier
        }

        // Supprimer le produit du panier
        $entityManager->remove($panierItem);
        $entityManager->flush();

        // Rediriger vers la page du panier ou une autre page appropriée après la suppression
        return $this->redirectToRoute('achat_produit');
    }


}
