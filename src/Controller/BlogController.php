<?php

namespace App\Controller;

use App\Entity\Commentaires;

use App\Entity\Users;
use App\Entity\Publications;
use App\Form\AddPostType;
use App\Form\UpdatePostType;
use App\Repository\PublicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reactions;



class BlogController extends AbstractController
{
     #[Route('/feed', name: 'app_feed', methods: ['GET', 'POST'])]
     public function feed(Request $request, PublicationRepository $publicationRepository, SessionInterface $session): Response
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
                        // You can log or display an error message here
                    }
                }
            
                // Fetch the user from the database
                $userRepository = $this->getDoctrine()->getRepository(Users::class);
                $userFromDb = $userRepository->find($user->getIdUser());

                // Set the user for the publication
                $publication->setUser($userFromDb);

                // Save the entity to the database
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($publication);
                $entityManager->flush();

                // Redirect to the index page or any other page as needed
                return $this->redirectToRoute('afficher_publications');
            }

            // Fetch existing publications
            $publications = $publicationRepository->findAll();

            return $this->render('publications/afficherPublications.html.twig', [
                'publications' => $publications,
                'form' => $form->createView(),
                'user' => $user,
            ]);
        } else {
            // Handle the case where the user is not logged in
            // You might want to redirect the user to the login page or display an error message
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
  

   
    #[Route('/user', name: 'app_user', methods: ['GET', 'POST'])]
    public function getUserFromSession(SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');
        // Check if a user is logged in
        if ($user instanceof Users) {
            return $this->render('feed.html.twig', [
                'user' => $user,
            ]);
        } else {
            // Handle the case where the user is not logged in
            // You might want to redirect the user to the login page or display an error message
            return $this->redirectToRoute('app_login');
        }
    }


    #[Route('/start', name: 'start', methods: ['GET', 'POST'])]
    public function start(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

}