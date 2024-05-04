<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Users;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Publications;
use App\Form\AddPostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\EvenementsRepository;
use App\Entity\Evenements;
use App\Form\EvenementsType;
use App\Repository\ParticipationsRepository;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;



class AdminController extends AbstractController
{
   #[Route('/admin', name: 'app_admin')]
    public function index(SessionInterface $session): Response
    {
        $evenements = $this->getDoctrine()->getRepository(Evenements::class)->findAll();
        $nombreTotalEvenements = count($evenements);

            // Retrieve the user from the session
            $user = $session->get('user');

            // Check if a user is logged in
            if ($user instanceof Users) {
                //check to logged in user is admin
                if($user->getRole() ===  'ROLE_ADMIN'){
                    return $this->render('admin/home.html.twig', [
                        'controller_name' => 'AdminController',
                        'user' => $user,
                        'nombreTotalEvenements' => $nombreTotalEvenements,

                    ]);

                }else{
                    return $this->redirectToRoute('afficher_publications');
                }
            } else {
                return $this->redirectToRoute('app_login');
            }
    }   
    
    #[Route('/admin/users', name: 'app_admin_users', methods:['GET'])]
    public function getAllUsers(SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
    return $this->render('admin/users.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
    ]);
 } else {
    return $this->redirectToRoute('app_login');
 }
    }
    

   /* #[Route('/admin/posts', name: 'app_admin_users', methods:['GET'])]
    public function getAllPosts(PublicationRepository $publicationRepository,SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
            // Fetch publications by their owner
            $publications = $publicationRepository->findAll();
    return $this->render('admin/posts.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
        'publications' => $publications,
    ]);
} else {
    return $this->redirectToRoute('app_login');
}
    }
    */
    #[Route('/admin/posts', name: 'app_admin_users', methods: ['GET', 'POST'])]
    public function getAllPosts(PublicationRepository $publicationRepository,SessionInterface $session, Request $request): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');
        
        // Check if a user is logged in
        if ($user instanceof Users) {
            $publication = new Publications();
            
            $form = $this->createForm(AddPostType::class, $publication);
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

            // Fetch existing publications
            $publications = $publicationRepository->findAll();

            return $this->render('admin/posts.html.twig', [
                'publications' => $publications,
                'form' => $form->createView(),
                'user' => $user,
            ]);
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
    #[Route('/admin/groups', name: 'app_admin_groups', methods:['GET'])]
    public function getAllGroups(SessionInterface $session): Response
    {
       
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
    return $this->render('admin/groups.html.twig', [
        'controller_name' => 'AdminController',
        'user' => $user,
    ]);
} else {
    return $this->redirectToRoute('app_login');
}
    }

//////////////////////////////////////////////////////////////ADMIN EVENT//////////////////////////////////////////////////////////////
    #[Route('/admin/events', name: 'app_admin_events', methods:['GET', 'POST'])]
    public function getAllEvents(EvenementsRepository $evenementsRepository,SessionInterface $session, Request $request, ParticipationsRepository $participationRepository): Response
    {
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
         
        // Retrieve the user from the session
        $user = $session->get('user');
        
        // Check if a user is logged in
        if ($user instanceof Users) {
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
        } else {
            // Handle the case where the user is not logged in
                return $this->redirectToRoute('app_login');
        }
    }


    
    #[Route('/evenements/{id}', name: 'evenements_details', methods: ['GET'])]
    public function getEventDetails(int $id): JsonResponse
    {
        $event = $this->getDoctrine()->getRepository(Evenements::class)->find($id);

        if (!$event) {
            return new JsonResponse(['error' => 'Événement non trouvé'], 404);
        }

        // Construisez le tableau des détails de l'événement
        $details = [
            'image' => $event->getImage(),
            'titre' => $event->getTitreEvenement(),
            'dateDebut' => $event->getDDebutEvenement()->format('Y-m-d H:i'),
            'dateFin' => $event->getDFinEvenement()->format('Y-m-d H:i'),
            'lieu' => $event->getLieuEvenement(),
            'description' => $event->getDescriptionEvenement()
        ];

        // Retournez les détails sous forme de réponse JSON
        return new JsonResponse($details);
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
    


   
  
























    #[Route('/admin/produits', name: 'app_admin_users', methods:['GET'])]
    public function getAllProducts(SessionInterface $session): Response
    {
       
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/products.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
    }

    #[Route('/admin/reports', name: 'app_admin_users', methods:['GET'])]
    public function getAllReports(SessionInterface $session): Response
    {
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/reports.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
    }





}