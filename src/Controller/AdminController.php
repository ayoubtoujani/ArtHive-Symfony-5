<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Form\EditUserFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Users;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Publications;
use App\Entity\Reclamationgroupe;
use App\Form\AddPostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\ReclamationgroupeRepository;
use App\Services\PdfGenerator;
use App\Entity\Evenements;
use App\Form\EvenementsType;
use App\Repository\EvenementsRepository;
use App\Repository\ParticipationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use App\Form\ProduitType;
use App\Form\ModifierProduitType;








class AdminController extends AbstractController
{

    /////////////////////////////////////////**HOME */
  /*  #[Route('/admin', name: 'app_admin')]
    public function index(SessionInterface $session): Response
    {
        $evenements = $this->getDoctrine()->getRepository(Evenements::class)->findAll();
        $nombreTotalEvenements = count($evenements);

           // Récupérer les produits depuis la base de données
           $produits = $this->getDoctrine()->getRepository(Produits::class)->findAll();
           $nombreTotalProduits = count($produits);

            // Retrieve the user from the session
            $user = $session->get('user');

            // Check if a user is logged in
            if ($user instanceof Users) {
                //check to logged in user is admin
                if($user->getRole() ===  'ROLE_ADMIN'){
                    //first lets count all the posts in the database
                    $publicationRepository = $this->getDoctrine()->getRepository(Publications::class);
                    $publications = $publicationRepository->findAll();
                    $countPosts = count($publications);
                    $groupRepository = $this->getDoctrine()->getRepository(Groups::class);
                    $groups = $groupRepository->findAll();
                    $countGroups = count($groups);
                    return $this->render('admin/home.html.twig', [
                        'controller_name' => 'AdminController',
                        'user' => $user,
                        'countPosts' => $countPosts,
                        'countGroups' => $countGroups,
                        'nombreTotalEvenements' => $nombreTotalEvenements,
                        'nombreTotalProduits' => $nombreTotalProduits,
                    ]);

                }else{
                    return $this->redirectToRoute('afficher_publications');
                }
            } else {
                return $this->redirectToRoute('app_login');
            }      
    }
    */
    /////////////////////////////////////////**POSTS */
    #[Route('/admin/posts', name: 'app_admin_users', methods: ['GET', 'POST'])]
    public function getAllPosts(PublicationRepository $publicationRepository,SessionInterface $session, Request $request): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');
        
        // Check if a user is logged in
        if ($user instanceof Users) {
            if($user->getRole() ===  'ROLE_ADMIN'){
            $publication = new Publications();
            
            $form = $this->createForm(AddPostType::class, $publication, ['validation_groups' => ['addPost']]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $urlFile = $form->get('urlFile')->getData();

                // Check if a file has been uploaded
                if ($urlFile) {


                    // Extracting file extension without relying on guesser
             

                    $originalFilename = pathinfo($urlFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $this->sanitizeFileName($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$urlFile->getClientOriginalExtension();


                
                    // Move the file to the directory where images are stored
                    try {
                        $urlFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Handle file upload error

                        throw new \Exception('Error uploading file');
                    }
                }
            
                // Fetch the user from the database
                $userRepository = $this->getDoctrine()->getRepository(Users::class);
                $userFromDb = $userRepository->find($user->getIdUser());

                // Set the user for the publication
                $publication->setUser($userFromDb);
                // Set the file name for the publication
                $publication->setUrlFile($newFilename);
                // Set the date of creation for the publication to the current date and time
                $publication->setDCreationPublication(new \DateTime());
                // Save the entity to the database

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($publication);
                $entityManager->flush();

                // Redirect to the index page or any other page as needed
                return $this->redirectToRoute('app_admin_posts');
            }

            // Fetch existing publications from the database and display them in the template in reverse order
            $publications = $publicationRepository->findBy([], ['dCreationPublication' => 'DESC']);

            return $this->render('admin/posts.html.twig', [
                'publications' => $publications,
                'form' => $form->createView(),
                'user' => $user,
            ]);
            }else{
                return $this->redirectToRoute('afficher_publications');
            }
        } else {
            // Handle the case where the user is not logged in
                return $this->redirectToRoute('app_login');
        }
    }
    private function sanitizeFileName($fileName) {
        // Remove special characters and spaces
        $fileName = preg_replace('/[^A-Za-z0-9_.-]/', '', $fileName);
        // Convert spaces to underscores
        $fileName = str_replace(' ', '_', $fileName);
        return $fileName;
    }
    
    #[Route('/admin/groups', name: 'app_admin_groups', methods:['GET'])]
    public function getAllGroups(SessionInterface $session): Response
    {  
        // Retrieve the user from the session
        $user = $session->get('user');
        // Check if a user is logged in
        if ($user instanceof Users) {
            if($user->getRole() ===  'ROLE_ADMIN'){
            

            $groups = $this->getDoctrine()->getRepository(Groups::class)->findAll();
            $nbr = count($groups);
            
        

                    return $this->render('admin/groups.html.twig', [
                        'controller_name' => 'AdminController',
                        'user' => $user,
                        'nbr' => $nbr,
                        'groups' => $groups,
        ]);
    }else{
        return $this->redirectToRoute('app_login');
       
    }
}

    }
    
#[Route('/delete-publication-admin/{id}', name: 'delete_publication_by_admin', methods: ['GET', 'POST'])]
public function deletePublication($id, PublicationRepository $publicationRepository): RedirectResponse
{
    $publication = $publicationRepository->find($id);

    if (!$publication) {
        // Publication not found, handle this case (e.g., redirect or error message)
        return $this->redirectToRoute('app_admin_posts');
    }

    // Remove the publication from the database
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($publication);
    $entityManager->flush();

    // Redirect back to the My Publications page
    return $this->redirectToRoute('app_admin_posts');

}

//////////////////////////////////////////////////////////////ADMIN USERS//////////////////////////////////////////////////////////////

    #[Route('/admin/users', name: 'app_admin_users', methods:['GET'])]
    public function getAllUsers(SessionInterface $session): Response
    {

        // Retrieve the user from the session
        $user = $session->get('user');
        // Check if a user is logged in
        if ($user instanceof Users) {
            if($user->getRole() ===  'ROLE_ADMIN'){
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $users= $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'controller_name' => 'AdminController',
            'user' => $user,
        ]);
    }
    else{
        return $this->redirectToRoute('app_login');
    }
    } 
    }

    #[Route('/admin/user/edit/{id}', name: 'app_admin_user_edit', methods:['POST'])]
    public function edit(Request $request, $id)
    {
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user-edit.html.twig', [
            'user' => $user,
            'registerForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'app_admin_user_delete', methods:['POST'])]
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_users');
    }





   //////////////////////////////////////////////////////////////ADMIN EVENT//////////////////////////////////////////////////////////////
   #[Route('/admin/events', name: 'app_admin_events', methods:['GET', 'POST'])]
   public function getAllEvents(EvenementsRepository $evenementsRepository,SessionInterface $session, Request $request, ParticipationsRepository $participationRepository): Response
   {

    // Retrieve the user from the session
    $user = $session->get('user');
    // Check if a user is logged in
    if ($user instanceof Users) {
        if($user->getRole() ===  'ROLE_ADMIN'){
       $evenements = $this->getDoctrine()->getRepository(Evenements::class)->findAll();
       $nombreTotalEvenements = count($evenements);
       // Initialiser le tableau pour stocker le nombre de produits dans chaque catégorie
       $nombreEvenementsParCategorie = array(
          'PEINTURE' => 0, 
          'SCULPTURE' => 0,
          'MUSIQUE' => 0, 
          'CINEMA' => 0,
          'THEATRE' => 0,
          'PHOTOGRAPHIE' => 0,
          'ART_NUMERIQUE' => 0,
          'ART_URBAIN' => 0,
          'LITTERATURE' => 0,
       );
        // Parcourir tous les produits et mettre à jour le tableau avec le nombre de produits dans chaque catégorie
        foreach ($evenements as $evenement) {
           $categorieEvenement = $evenement->getCategorieevenement();
           if (array_key_exists($categorieEvenement, $nombreEvenementsParCategorie)) {
               $nombreEvenementsParCategorie[$categorieEvenement]++;
           }
       }
       $categorie = $request->query->get('categorie');
     
       // Filtrer les produits par catégorie si une catégorie est sélectionnée
       if ($categorie !== null) {
           $evenements = array_filter($evenements, function ($evenement) use ($categorie) {
               return $evenement->getCategorieevenement() === $categorie;
           });
       }




        // Récupérer le nombre de participants pour chaque événement
        $participantsCounts = [];
        foreach ($evenements as $evenement) {
            $participantsCounts[$evenement->getIdEvenement()] = $participationRepository->countByEvenement($evenement->getIdEvenement());
        }
        
       
           $evenements = new Evenements();
           
           $form = $this->createForm(EvenementsType::class, $evenements);
           $form->handleRequest($request);

           if ($form->isSubmitted() && $form->isValid()) {

               $imageFile = $form->get('image')->getData();

                // Vérifier si un fichier a été téléchargé
              if ($imageFile) {
               // Générer un nom de fichier unique pour éviter les conflits
               $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier vers le répertoire où vous souhaitez stocker les images
               try {
                $imageFile->move(
                 $this->getParameter('images_directory'),
                 $newFilename
                );
                } catch (FileException $e) {
           // Gérer l'erreur si le déplacement du fichier a échoué
            }         

     }            
               // Fetch the user from the database
               $userRepository = $this->getDoctrine()->getRepository(Users::class);
               $userFromDb = $userRepository->find($user->getIdUser());

               // Set the user for the publication
               $evenements->setIdUser($userFromDb);
               // Set the file name for the publication
               $evenements->setImage($newFilename);
               // Set the date of creation for the publication to the current date and time
               $evenements->setDDebutEvenement(new \DateTime());
               $evenements->setDFinEvenement(new \DateTime());

               // Save the entity to the database

               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($evenements);
               $entityManager->flush();

               // Redirect to the index page or any other page as needed
               return $this->redirectToRoute('app_admin_events');
           }

           // Fetch existing publications
           $evenements = $evenementsRepository->findAll();


           return $this->render('admin/events.html.twig', [
               'evenements' => $evenements,
               'form' => $form->createView(),
               'participantsCounts' => $participantsCounts,
               'user' => $user,
               'nombreTotalEvenements' => $nombreTotalEvenements,
               'nombreEvenementsParCategorie' => $nombreEvenementsParCategorie,


           ]);
        }else{
            return $this->redirectToRoute('app_login');
        }
    }
    }
        
   #[Route('/evenements-admin/{id}', name: 'evenements_delete_admin', methods: ['POST'])]
   public function deleteEventAdmin(Request $request, $id, EvenementsRepository $evenementsRepository, EntityManagerInterface $entityManager): Response
   {
       // Charger l'événement correspondant depuis le repository
       $evenement = $evenementsRepository->find($id);
       // Vérifier si l'événement existe
       if (!$evenement) {
           return new JsonResponse(['error' => 'Événement non trouvé.'], 404);
       }
   
       // Supprimer l'événement de la base de données
       $entityManager->remove($evenement);
       $entityManager->flush();
   
       // Envoyer une réponse JSON pour indiquer que la suppression a réussi
       return $this->redirectToRoute('app_admin_events', [], Response::HTTP_SEE_OTHER);
   }


   /////////////////////////////////////////**reports */
    #[Route('/admin/reports', name: 'app_admin_users', methods:['GET'])]
    public function getAllReports(SessionInterface $session): Response
    {
       // Retrieve the user from the session
       $user = $session->get('user');
         // Check if a user is logged in
    if ($user instanceof Users) {
        if($user->getRole() ===  'ROLE_ADMIN'){
        $reports = $this->getDoctrine()->getRepository(Reclamationgroupe::class)->findAll();
        $nbr = count($reports);
       
            return $this->render('admin/reports.html.twig', [
                'controller_name' => 'AdminController',
                'user' => $user,
                'nbr' => $nbr,
                'reports' => $reports,
            ]);
       }else {
        return $this->redirectToRoute('app_login');
       }
    }
    }

    #[Route('/admin/reports/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(ReclamationgroupeRepository $reclamationRepository, PdfGenerator $pdfGenerator): Response
    {
        
        // Récupérer toutes les réclamations
        $reports = $reclamationRepository->findAll();

        // Générer le HTML à partir Twig
        $html = $this->renderView('admin/reports_pdf.html.twig', [
            'reports' => $reports,

        ]);

        // Générer le PDF à partir du html
        $response = $pdfGenerator->generatePdfResponse($html);

        return $response;
    }


    /////////////////////////////////////::**PRODUCTS */

    #[Route('/admin/produits', name: 'app_admin_products', methods: ['GET', 'POST'])]
    public function getAllProducts(SessionInterface $session, Request $request): Response
    {
        // Récupérer les produits depuis la base de données
        $produits = $this->getDoctrine()->getRepository(Produits::class)->findAll();
        $nombreTotalProduits = count($produits);
        $nombreProduitsEnStock = 0;
        $nombreProduitsHorsStock = 0;

        foreach ($produits as $produit) {
            if ($produit->getStockProduit() > 0) {
                $nombreProduitsEnStock++;
            } else {
                $nombreProduitsHorsStock++;
            }
        }

        // Initialiser le tableau pour stocker le nombre de produits dans chaque catégorie
        $nombreProduitsParCategorie = array(
            'PEINTURE' => 0,
            'AI_ART' => 0,
            'PIXEL_ART' => 0,
            'SCULPTURE' => 0,
            'PHOTOGRAPHIE' => 0,
            'ANIME' => 0,
            'DESSIN' => 0,
            'DIGITAL_ART' => 0,
            'ILLUSTRATION' => 0,
            'AUTRE' => 0
        );

        // Parcourir tous les produits et mettre à jour le tableau avec le nombre de produits dans chaque catégorie
        foreach ($produits as $produit) {
            $categorieProduit = $produit->getCategProduit();
            if (array_key_exists($categorieProduit, $nombreProduitsParCategorie)) {
                $nombreProduitsParCategorie[$categorieProduit]++;
            }
        }

        $categorie = $request->query->get('categorie');
      
        // Filtrer les produits par catégorie si une catégorie est sélectionnée
        if ($categorie !== null) {
            $produits = array_filter($produits, function ($produit) use ($categorie) {
                return $produit->getCategProduit() === $categorie;
            });
        }

        // Calculer le prix maximal parmi tous les produits
        $maxPrix = $this->getDoctrine()->getRepository(Produits::class)->createQueryBuilder('p')
                ->select('MAX(p.prixProduit)')
                ->getQuery()
                ->getSingleScalarResult();
          
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
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
        }
    return $this->render('admin/products.html.twig', [
        'controller_name' => 'AdminController',
        'produits' => $produits,
        'form' => $form->createView(),
        'user' => $user,
        'maxPrix' => $maxPrix,
        'produit' => $produit,
        'nombreTotalProduits' => $nombreTotalProduits,
        'nombreProduitsEnStock' => $nombreProduitsEnStock,
        'nombreProduitsHorsStock' => $nombreProduitsHorsStock,
        'nombreProduitsParCategorie' => $nombreProduitsParCategorie,
    ]);
    } else {
    return $this->redirectToRoute('app_login');
    }
    }

    #[Route('/admin/produits/delete/{id}', name: 'delete_product', methods: ['GET', 'POST'])]
    public function deleteProduct($id): RedirectResponse
    
    {
        $produit = $this->getDoctrine()->getRepository(Produits::class)->find($id);

        if (!$produit) {
            // Redirection si le produit n'est pas trouvé
            return $this->redirectToRoute('app_admin_products');
        }

        // Supprimer le produit de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($produit);
        $entityManager->flush();

        // Redirection vers la page des produits
        return $this->redirectToRoute('app_admin_products');
    }

    #[Route('/admin/produits/{id}', name: 'modifier_produit_admin', methods: ['GET', 'POST'])]
    public function modifierProduitAdmin(int $id, Request $request, SessionInterface $session): Response
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
            return $this->redirectToRoute('app_admin_products');
        }

        // Afficher le formulaire de modification pré-rempli
        return $this->render('admin/modProdAD.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
            'user' => $user,
        ]);
    }

}