<?php
// src/Controller/EventController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EvenementsRepository;
use App\Entity\Evenements; // Importer la classe Evenements
use App\Entity\Users; // Importer la classe Users
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Form\EvenementsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Importez la classe SessionInterface
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\ParticipationsRepository;
use App\Entity\Participation;
use App\Form\EvenementsModificationType;
use Twilio\Rest\Client;
use Tattali\CalendarBundle\TattaliCalendarBundle;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Knp\Component\Pager\PaginatorInterface;


class EventController extends AbstractController
{
    /**
     * @Route("/events", name="app_events")
     */
     
     #[Route('/evenements', name: 'evenements_index', methods: ['GET', 'POST'])]
     public function index( EvenementsRepository $evenementsRepository, ParticipationsRepository $participationRepository,Request $request,EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
     {
         $evenements = $this->getDoctrine()->getRepository(Evenements::class)->findAll();
          // Récupérer la catégorie sélectionnée depuis la requête
          $categorie = $request->query->get('categorie');
           // Filtrer les produits par catégorie si une catégorie est sélectionnée
           if ($categorie !== null) {
            $evenements = array_filter($evenements, function ($evenement) use ($categorie) {
                return $evenement->getCategorieevenement() === $categorie;
            });
        }
        // Récupérer tous les événements de la semaine en utilisant la méthode de votre repository
          $eventsThisWeek = $evenementsRepository->findEventsThisWeek();         // Récupérer le nombre de participants pour chaque événement
          $participantsCounts = [];
          foreach ($evenements as $evenement) {
              $participantsCounts[$evenement->getIdEvenement()] = $participationRepository->countByEvenement($evenement->getIdEvenement());
          }
        
     $repository = $this->getDoctrine()->getRepository(Evenements::class);
 
     $evenements = $entityManager->getRepository(Evenements::class)->findBy([], ['dDebutEvenement' => 'DESC']);
     $query = $this->getDoctrine()
     ->getRepository(Evenements::class)
     ->createQueryBuilder('e')
     ->orderBy('e.dDebutEvenement', 'DESC') // Tri par date de début d'événement
     ->getQuery();
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de la page, récupéré depuis la requête GET
            9 // Nombre d'éléments par page
        );
     
 
         return $this->render('evenements/index.html.twig', [
            'eventsThisWeek' => $eventsThisWeek,
             'evenements' => $evenements,
             'pagination' => $pagination,
             'participantsCounts' => $participantsCounts,
 
 
 
         ]);
     }
   

    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
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

    #[Route("/vos-evenements", name: "vos_evenements", methods: ['GET', 'POST'])]
    public function vosEvenements(Request $request, EntityManagerInterface $entityManager, EvenementsRepository $evenementsRepository,SessionInterface $session, FlashyNotifier $flashy): Response
    {
        // Récupérer l'utilisateur connecté à partir du paramètre de requête
        $user = $session->get('user');
        // Récupérer les événements de l'utilisateur connecté
        $evenements = $this->getDoctrine()->getRepository(Evenements::class)->findBy(['idUser' => $user]);
        // Créer un nouvel objet Evenements pour le formulaire
        $evenement = new Evenements();
        $form = $this->createForm(EvenementsType::class, $evenement);
        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
        
            // Ajuster les données de l'événement si nécessaire (par exemple, lier l'utilisateur connecté)
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $userFromDb = $userRepository->find($user->getIdUser());
            $evenement->setIdUser($userFromDb);
             // Récupérer le fichier de l'image depuis la requête
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

        // Enregistrer le nom du fichier dans l'objet Evenements
        $evenement->setImage($newFilename);
        
    }
            // Persister et flusher l'événement
            $entityManager->persist($evenement);
            $entityManager->flush();
            //$this->addFlash('success', 'Votre évènement a été ajoutée avec succès !');
            $session->getFlashBag()->add('success', 'Évènement ajoutée avec succès !');

            $flashy->success('Event created!', 'http://your-awesome-link.com');
            // Par exemple, rediriger vers la page affichant les événements de l'utilisateur
            return $this->redirectToRoute('vos_evenements');
        }
      
        $evenements = $evenementsRepository->findBy(['idUser' => $user]);

        // Passer les événements et le formulaire à la vue pour les afficher
        return $this->render('evenements/vos_evenements.html.twig', [
            'evenements' => $evenements,
            'form' => $form->createView(), // Passer la vue du formulaire à la vue
            'user' => $user,
        ]);
    }

    #[Route('/evenement/modifier/{id}', name: 'modifier_evenement')]
    public function modifierEvenement(int $id, Request $request): Response
    {
        // Récupérer le produit à modifier depuis la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $evenement = $entityManager->getRepository(Evenements::class)->find($id);

        // Vérifier si le produit existe
        if (!$evenement) {
            throw $this->createNotFoundException('event not found');
        }
        $isRequired = false; // Par défaut, le champ d'image n'est pas obligatoire lors de la modification

        // Créer le formulaire de modification
        $form = $this->createForm(EvenementsModificationType::class, $evenement, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        // Traiter la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
                // Enregistrer à nouveau le produit avec l'image mise à jour
                $entityManager->persist($evenement);
                $entityManager->flush();            return $this->redirectToRoute('vos_evenements');

            }

            // Rediriger vers une page appropriée (par exemple, la liste des produits de l'utilisateur)
        // Afficher le formulaire de modification pré-rempli
        return $this->render('evenements/editEvenement.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }


    


 

    #[Route('/evenements/{id}', name: 'evenements_delete', methods: ['POST'])]
public function deleteEvent(Request $request, $id, EvenementsRepository $evenementsRepository, EntityManagerInterface $entityManager,FlashyNotifier $flashy,SessionInterface $session): Response
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
    $session->getFlashBag()->add('success', 'Évènement supprimé avec succès !'); //stocke les messages flash
  // $this->addFlash('success', 'Événement supprimé avec succès !');
   $flashy->success('Évènement supprimé avec succès !');
  //$this->get('flashy.notifier')->success('Événement supprimé avec succès');


   // Envoyer une réponse JSON pour indiquer que la suppression a réussi
    return $this->redirectToRoute('vos_evenements', [], Response::HTTP_SEE_OTHER);
}


     #[Route("/participation/{eventId}", name: "participation", methods: ["POST"])]
    public function participation(Request $request, $eventId): JsonResponse
    {
        // Récupérer l'action de participation à partir de la requête AJAX
        $action = $request->request->get('action');

        // Votre logique pour mettre à jour la participation dans la base de données en fonction de $action et $eventId

        // Supposons que vous ayez une fonction pour mettre à jour la participation dans votre modèle de données
        $participantsCount = 0; // Nombre de participants mis à jour
        $participationStatus = 'participated'; // Statut de participation mis à jour

        // Supposons également que vous ayez une méthode dans votre modèle de données pour récupérer le nombre de participants
        // $participantsCount = $this->getDoctrine()->getRepository(Evenement::class)->getParticipantsCount($eventId);

        // Retourner une réponse JSON avec le nombre de participants mis à jour et le nouveau statut de participation
        return $this->json([
            'success' => true,
            'participantsCount' => $participantsCount,
            'participationStatus' => $participationStatus,
        ]);
    }

    

    #[Route('/evenement/participate/{eventId}', name: 'event_participate', methods: ['POST'])]
    public function participate(int $eventId, EntityManagerInterface $entityManager, SessionInterface $session, EvenementsRepository $evenementsRepository): Response
    {
        // Récupérer l'utilisateur connecté depuis la session
        $user = $session->get('user');
       
// Vérifier si l'utilisateur est connecté
if (!$user instanceof Users) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    return $this->redirectToRoute('app_login');
}
       // Récupérer l'événement
        $event = $evenementsRepository->find($eventId);
 
        // Vérifier si l'événement existe
        if (!$event) {
            throw $this->createNotFoundException('L\'événement spécifié n\'existe pas.');
        }
           // Initialiser le client Twilio avec les identifiants de votre compte
    $twilioAccountSid = $this->getParameter('twilio_account_sid');
    $twilioAuthToken = $this->getParameter('twilio_auth_token');
    $twilioPhoneNumber = $this->getParameter('twilio_phone_number');
    $twilioClient = new Client($twilioAccountSid, $twilioAuthToken);

        // Récupérer l'ID de l'utilisateur à partir de l'objet récupéré
        $userId = $user->getIdUser();

        // Utiliser l'ID pour charger l'utilisateur à partir de la base de données
        $loggedInUser = $entityManager->getRepository(Users::class)->find($userId);

        // Vérifier si l'utilisateur a été trouvé
     if (!$loggedInUser) {
      throw $this->createNotFoundException('Utilisateur introuvable.');
     }
    
        // Récupérer les participations de l'utilisateur pour cet événement
        $existingParticipation = $entityManager->getRepository(Participation::class)->findOneBy([
            'idUser' => $userId,
            'idEvenement' => $event->getIdEvenement()
        ]);
    
        // Si l'utilisateur participe déjà, supprimer sa participation. Sinon, ajouter une nouvelle participation.
        if ($existingParticipation) {
            $entityManager->remove($existingParticipation);
            $action = 'removed';
        } else {

            $participation = new Participation();
            $participation->setIdUser($loggedInUser);
            $participation->setIdEvenement($event);
            $entityManager->persist($participation); 
            $action = 'added';      
            // Envoie du SMS pour la première participation
            $messageBody = "Vous avez participé à l'événement. Réservez la date : " . $event->getDDebutEvenement()->format('Y-m-d H:i');
            $twilioClient->messages->create(
                '+21692978106', // Replace with the recipient's phone number
            [
                'from' => $twilioPhoneNumber,
                'body' => $messageBody
            ]

            );   
        }           

        $entityManager->flush();    
        // Répondre avec un message indiquant l'action effectuée
        return new Response('Participation ' . $action . ' avec succès.', Response::HTTP_OK);
    }
    
    
    


    #[Route('/evenement/search', name: 'app_evenement_search')]
     public function search (Request $request,EvenementsRepository $evenementsRepository, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q'); //paramètres de requête dans l'URL
        $eventsThisWeek = $evenementsRepository->findEventsThisWeek();         // Récupérer le nombre de participants pour chaque événement

        $evenements = $this->getDoctrine()
            ->getRepository(Evenements::class)
            ->createQueryBuilder('p')
            ->where('p.titreEvenement LIKE :term')
            ->orwhere('p.categorieevenement LIKE :term')
            ->orwhere('p.lieuEvenement LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->orderBy('p.dDebutEvenement', 'DESC') // Tri par date de début d'événement
            ->getQuery()
            ->getResult();
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $evenements, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de la page, récupéré depuis la requête GET
            9 // Nombre d'éléments par page
        );

        // Passer les produits filtrés au template Twig
        return $this->render('evenements/index.html.twig', [
            'evenements' => $evenements,
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
            'eventsThisWeek' => $eventsThisWeek,
        ]);
    }
    #[Route("/eventsThisWeek", name:"events_this_week")]
    
    public function eventsThisWeek(EvenementsRepository $evenementsRepository): Response
    {
      // Récupérer les événements de la semaine en utilisant une méthode de votre repository
      $eventsThisWeek = $evenementsRepository->findEventsThisWeek();

      return $this->render('evenements/index.html.twig', [
          'eventsThisWeek' => $eventsThisWeek,
      ]);
    }
   
}