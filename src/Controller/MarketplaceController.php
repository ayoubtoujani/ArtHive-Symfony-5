<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produits;
use App\Form\ProduitType;
use App\Entity\Users;
use App\Entity\Panier;
use App\Entity\Productrating;
use App\Form\ModifierProduitType;
use Knp\Component\Pager\PaginatorInterface;





class MarketplaceController extends AbstractController
{
    
    #[Route('/marketplace', name: 'app_marketplace') ]
    public function index(): Response
    {
        return $this->render('marketplace/marketPlace.html.twig', [
            'controller_name' => 'MarketplaceController',
        ]);
    }

    #[Route('/marketplace', name: 'app_marketplace', methods: ['GET', 'POST'])]
    public function add(Request $request, SessionInterface $session, PaginatorInterface $paginator): Response
    {
          // Récupérer les produits depuis la base de données trier
          $queryBuilder = $this->getDoctrine()->getRepository(Produits::class)->createQueryBuilder('p')
          ->orderBy('p.dPublicationProduit', 'DESC');
    
          // Récupérer la catégorie sélectionnée depuis la requête
          $categorie = $request->query->get('categorie');

         
  
          // Filtrer les produits par catégorie si une catégorie est sélectionnée
          if ($categorie !== null) {
            $queryBuilder->andWhere('p.categProduit = :categorie')
                ->setParameter('categorie', $categorie);
        }
          
          $query = $queryBuilder->getQuery();
            $produits = $query->getResult();

               // Paginer les résultats
               $produits = $paginator->paginate(
                $produits, // Requête à paginer
                $request->query->getInt('page', 1), // Numéro de page
                9 // Nombre d'éléments par page
               );
            
            
          // Calculer le prix maximal parmi tous les produits
          $maxPrix = $this->getDoctrine()->getRepository(Produits::class)->createQueryBuilder('p')
                  ->select('MAX(p.prixProduit)')
                  ->getQuery()
                  ->getSingleScalarResult();
            
        // Check if a user is logged in
        $user = $session->get('user');
       
        if ($user instanceof Users) {
           
            $produit = new Produits();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('imageProduit')->getData();

            // Check if a file has been uploaded
            if ($imageFile) {
                // Generate a unique name for the file before saving it
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                    // You can log or display an error message here
                }

                // Store the file name in the entity
                $produit->setImageProduit($newFilename);
            }

            // Fetch the user from the database
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $userFromDb = $userRepository->find($user->getIdUser());

            // Set the user for the publication
            $produit->setUser($userFromDb);

            // Save the entity to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            
           

            // Redirect to the index page or any other page as needed
            return $this->redirectToRoute('app_marketplace');
        }

            return $this->render('marketplace/marketplace.html.twig', [
                'produits' => $produits,
                'form' => $form->createView(),
                'user' => $user,
                'maxPrix' => $maxPrix, 
                'categorie' => $categorie,
            ]);
        } else {
            // Handle the case where the user is not logged in
            // You might want to redirect the user to the login page or display an error message
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/acheter/{name}', name: 'achat_produit')]
    public function acheterProduit($name, SessionInterface $session): Response
    {
        // Récupérer l'EntityManager
        $entityManager = $this->getDoctrine()->getManager();
    
        // Récupérer les détails du produit correspondant au nom
        $produit = $this->getDoctrine()->getRepository(Produits::class)->findOneBy(['nomProduit' => $name]);
    
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');
    
        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer les produits du panier de l'utilisateur connecté
        $panierItems = $this->getDoctrine()->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);
    
        // Récupérer les détails des produits à partir des enregistrements du panier
        $products = [];
        foreach ($panierItems as $item) {
            $product = $this->getDoctrine()->getRepository(Produits::class)->find($item->getIdProduit());
            if ($product) {
                $products[] = $product;
            }
        }
    
        // Calculer le total du panier
        $totalPrix = $this->calculerTotalPanier($user, $entityManager);
    
        // Passer les détails du produit et du panier au template Twig
        return $this->render('marketplace/achat.html.twig', [
            'produit' => $produit,
            'products' => $products,
            'user' => $user,
            'totalPrix' => $totalPrix,
        ]);
    }
    
    


    
    #[Route('/marketplace/search', name: 'app_marketplace_search')]
     public function search(Request $request, SessionInterface $session,  PaginatorInterface $paginator): Response
    {
        $user = $session->get('user');
        $searchTerm = $request->query->get('q');

        $produits = $this->getDoctrine()
            ->getRepository(Produits::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->where('p.nomProduit LIKE :term')
            ->orwhere('p.categProduit LIKE :term')
            ->orWhere('u.nomUser LIKE :term')
            ->orWhere('u.prenomUser LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

            // Paginer les résultats de recherche
          $produits = $paginator->paginate(
            $produits, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page
            9 // Nombre d'éléments par page
        );

        $maxPrix = $this->getDoctrine()->getRepository(Produits::class)->createQueryBuilder('p')
            ->select('MAX(p.prixProduit)')
            ->getQuery()
            ->getSingleScalarResult();  
            
         // Créer une instance de formulaire vide pour éviter l'erreur Twig
        $form = $this->createForm(ProduitType::class);      

        // Passer les produits filtrés au template Twig
        return $this->render('marketplace/marketPlace.html.twig', [
            'produits' => $produits,
            'searchTerm' => $searchTerm,
            'maxPrix' => $maxPrix,
            'user' => $user,
            'form' => $form->createView(),
            
        ]);
    }
    
    #[Route('/vosproduits', name: 'vos_produits')]
    public function vosProduits(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');
        // Vérifier si l'utilisateur est connecté
        if ($user instanceof Users) {
            // Récupérer les produits de l'utilisateur connecté
            $userId = $user->getIdUser(); // Correction de la méthode à appeler
            $produits = $this->getDoctrine()->getRepository(Produits::class)->findBy(['user' => $userId]);
            // Passer les produits au template Twig
            return $this->render('marketplace/vosproduit.html.twig', [
                'produits' => $produits,
                'user' => $user,
            ]);
        } else {
            // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
            return $this->redirectToRoute('app_login');
        }
    }



    #[Route('/produit/supprimer/{id}', name: 'supprimer_produit')]
    public function supprimerProduit(int $id, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the product entity by its primary key
        $produit = $entityManager->getRepository(Produits::class)->find($id);

        // If the product doesn't exist, handle this accordingly, e.g., show an error message or redirect
        if (!$produit) {
            throw $this->createNotFoundException('Product not found');
        }

        // Remove the product from the database
        $entityManager->remove($produit);
        $entityManager->flush();
        

        // Redirect to the appropriate route after deletion
        return $this->redirectToRoute('vos_produits');
    }

    #[Route('/produit/modifier/{id}', name: 'modifier_produit')]
    public function modifierProduit(int $id, Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        // Récupérer le produit à modifier depuis la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produits::class)->find($id);

        // Vérifier si le produit existe
        if (!$produit) {
            throw $this->createNotFoundException('Product not found');
        }

        // Créer le formulaire de modification
        $form = $this->createForm(ModifierProduitType::class, $produit);
        
        $form->handleRequest($request);

        // Traiter la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();
                // Enregistrer à nouveau le produit avec l'image mise à jour
                $entityManager->persist($produit);
                $entityManager->flush();
            // Rediriger vers une page appropriée (par exemple, la liste des produits de l'utilisateur)
            return $this->redirectToRoute('vos_produits');
        }

        // Afficher le formulaire de modification pré-rempli
        return $this->render('marketplace/modifierProduit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'user' => $user,
        ]);
    }

    #[Route('/ajouter-au-panier/{id}/{quantity}', name: 'ajouter_au_panier')]
    public function ajouterAuPanier(int $id, int $quantity, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // Gérer le cas où l'utilisateur n'est pas connecté, par exemple, rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le produit à ajouter au panier
        $produit = $entityManager->getRepository(Produits::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Vérifier si la quantité demandée est disponible en stock
        if ($quantity > $produit->getStockProduit()) {
            // Gérer le cas où la quantité demandée est supérieure au stock disponible
            return new Response('Stock insuffisant', Response::HTTP_BAD_REQUEST);
        }

        // Créer une nouvelle instance de l'entité Panier
        $panier = new Panier();
        $panier->setIdUser($user->getIdUser()); // Associer l'utilisateur au panier
        $panier->setIdProduit($produit->getIdProduit()); // Associer le produit au panier

        // Vous pouvez également définir d'autres propriétés du panier ici, telles que la quantité, le prix, etc.

        // Persist the Panier entity
        $entityManager->persist($panier);
        $entityManager->flush();

        // Mettre à jour le stock du produit
        $produit->setStockProduit($produit->getStockProduit() - $quantity);
        $entityManager->flush();

        return new Response('Produit ajouté au panier', Response::HTTP_OK);
    }

    #[Route('/supprimer-panier', name: 'supprimer_panier')]
    public function supprimerPanier(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // Gérer le cas où l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les enregistrements du panier associés à l'utilisateur connecté
        $panierItems = $entityManager->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);

        // Supprimer tous les enregistrements du panier de l'utilisateur
        foreach ($panierItems as $panierItem) {
            $entityManager->remove($panierItem);
        }
        $entityManager->flush();

        // Répondre avec une réponse appropriée (par exemple, un message de confirmation)
        return new Response('Le panier a été vidé avec succès', Response::HTTP_OK);
    }

    #[Route('/supprimer-produit-panier/{id}', name: 'supprimer-produit-panier')]
    public function supprimerDuPanier(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');
    
        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // Gérer le cas où l'utilisateur n'est pas connecté, par exemple, rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer l'entrée correspondante dans la table du panier
        $panierItem = $entityManager->getRepository(Panier::class)->findOneBy([
            'idUser' => $user->getIdUser(),
            'idProduit' => $id
        ]);
    
        // Vérifier si l'élément du panier existe
        if ($panierItem) {
            // Récupérer le produit correspondant à l'élément du panier
            $produit = $entityManager->getRepository(Produits::class)->find($panierItem->getIdProduit());
    
            if ($produit) {
                // Mettre à jour le stock du produit
                $produit->setStockProduit($produit->getStockProduit() + 1);
                $entityManager->flush();
            }
    
            // Supprimer l'entrée du panier
            $entityManager->remove($panierItem);
            $entityManager->flush();
        }
    
        // Répondre avec une réponse appropriée (par exemple, une réponse JSON indiquant que la suppression a réussi)
        return new JsonResponse(['message' => 'Le produit a été supprimé du panier avec succès']);
    }

    



    #[Route('/cartCount', name: 'cartCount')]
    public function getNombreProduitsPanier(Request $request, SessionInterface $session): JsonResponse
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof Users) {
            // rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Obtenez les éléments du panier pour l'utilisateur connecté
        $panierItems = $this->getDoctrine()->getRepository(Panier::class)->findBy(['idUser' => $user->getIdUser()]);

        // Initialisez un compteur pour le nombre de produits
        $nombreProduits = count($panierItems);

        // Retournez le nombre de produits dans le panier
        return new JsonResponse(['nombreProduits' => $nombreProduits]);
    }
        

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
   
   


   

  

   


 




}   