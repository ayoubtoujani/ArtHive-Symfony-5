<?php

namespace App\Controller;

use App\Repository\GroupsRepository;
use App\Repository\ReclamationgroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Users;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Services\PdfGenerator;


class AdminController extends AbstractController
{
//    #[Route('/admin', name: 'app_admin')]
//    public function index(SessionInterface $session): Response
//    {
//
//        // Retrieve the user from the session
//        $user = $session->get('user');
//
//        // Check if a user is logged in
//        if ($user instanceof Users) {
//            //check to logged in user is admin
//            if($user->getRole() ===  'ROLE_ADMIN'){
//                return $this->render('admin/home.html.twig', [
//                    'controller_name' => 'AdminController',
//                    'user' => $user,
//                ]);
//
//            }else{
//                return $this->redirectToRoute('afficher_publications');
//            }
//        } else {
//            return $this->redirectToRoute('app_admin_reports');
//        }
//
//
//
//
//    }

    #[Route('/admin', name: 'app_admin')]
    public function index(SessionInterface $session, GroupsRepository $groupsRepository): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
            // Get the number of groups from the repository
            $numberOfGroups = count($groupsRepository->findAll());

            // Render the admin homepage with the number of groups
            return $this->render('admin/home.html.twig', [
                'controller_name' => 'AdminController',
                'user' => $user,
                'numberOfGroups' => $numberOfGroups,
            ]);
        } else {
            return $this->redirectToRoute('app_admin_reports');
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
            return $this->redirectToRoute('app_admin_reports');
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
     return $this->redirectToRoute('app_admin_reports');
 }
     }
     */



    #[Route('/admin/groups', name: 'app_admin_groups', methods: ['GET'])]
    public function getAllGroups(GroupsRepository $groupsRepository, SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');

        // Check if a user is logged in
        if ($user instanceof Users) {
            // Get the number of groups from the repository
            $numberOfGroups = count($groupsRepository->findAll());

            $groups = $groupsRepository->findAll();

            return $this->render('admin/groups.html.twig', [
                'controller_name' => 'AdminController',
                'user' => $user,
                'groups' => $groups,
                'numberOfGroups' => $numberOfGroups, // Transmit numberOfGroups to the view
            ]);
        } else {
            return $this->redirectToRoute('app_admin_reports');
        }
    }


    #[Route('/admin/reports', name: 'app_admin_reports', methods:['GET'])]
    public function getAllReports(ReclamationgroupeRepository $reclamationRepository, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur depuis la session
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if ($user instanceof Users) {
            // Récupérer toutes les réclamations
            $reports = $reclamationRepository->findAll();

            // Rendre la vue des rapports avec les données récupérées
            return $this->render('admin/reports.html.twig', [
                'controller_name' => 'AdminController',
                'user' => $user,
                'reports' => $reports,
            ]);
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_admin_reports');
        }
    }
    #[Route('/admin/reports/generate-pdf', name: 'generate_pdf')]
    public function generatePdf(ReclamationgroupeRepository $reclamationRepository, PdfGenerator $pdfGenerator, SessionInterface $session): Response
    {
        $user = $session->get('user');

        // Check if a user is logged in

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

}
