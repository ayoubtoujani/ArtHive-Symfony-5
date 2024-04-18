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


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(SessionInterface $session): Response
    {

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
                    return $this->render('admin/home.html.twig', [
                        'controller_name' => 'AdminController',
                        'user' => $user,
                        'countPosts' => $countPosts,
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

            // Fetch existing publications from the database and display them in the template in reverse order
            $publications = $publicationRepository->findBy([], ['dCreationPublication' => 'DESC']);

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


    #[Route('/admin/events', name: 'app_admin_users', methods:['GET'])]
    public function getAllEvents(SessionInterface $session): Response
    {
       // Retrieve the user from the session
       $user = $session->get('user');

       // Check if a user is logged in
       if ($user instanceof Users) {
   return $this->render('admin/events.html.twig', [
       'controller_name' => 'AdminController',
       'user' => $user,
   ]);
} else {
   return $this->redirectToRoute('app_login');
}
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
