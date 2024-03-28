<?php

namespace App\Controller;
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


class PublicationsController extends AbstractController
{
    #[Route('/publications', name: 'afficher_publications', methods: ['GET', 'POST'])]
    public function add(Request $request, PublicationRepository $publicationRepository, SessionInterface $session): Response
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
    #[Route('/my', name: 'my_publications')]
    public function myPub(Request $request,PublicationRepository $publicationRepository, SessionInterface $session): Response
    {
        // Retrieve the user from the session
        $user = $session->get('user');
        // Check if a user is logged in
        if ($user instanceof Users) {
            // Fetch publications by their owner
            $publications = $publicationRepository->findBy(['user' => $user]);
            return $this->render('publications/myPublications.html.twig', [
                'publications' => $publications,
                'user' => $user,   
            ]);
        }
}
#[Route('/delete-publication/{id}', name: 'delete_publication')]
public function deletePublication($id, PublicationRepository $publicationRepository): RedirectResponse
{
    $publication = $publicationRepository->find($id);

    if (!$publication) {
        // Publication not found, handle this case (e.g., redirect or error message)
        return $this->redirectToRoute('my_publications');
    }

    // Remove the publication from the database
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($publication);
    $entityManager->flush();

    // Redirect back to the My Publications page
    return $this->redirectToRoute('my_publications');

}
#[Route('/update-post/{id}', name: 'update_post', methods: ['GET', 'POST'])]
public function updatePost($id , Request $request, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository,SessionInterface $session): Response
{

    //
    $user = $session->get('user');
    if (!$user instanceof Users) {
        // Handle the case where the user is not logged in
        // You might want to redirect the user to the login page or display an error message
        return $this->redirectToRoute('app_login');
    }
    $publication = $publicationRepository->find($id);

    if (!$publication) {
        // Publication not found, handle this case (e.g., redirect or error message)
        return $this->redirectToRoute('my_publications');
    }

    $form = $this->createForm(UpdatePostType::class, $publication);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Publication updated successfully.');

        return $this->redirectToRoute('my_publications');
    }

    return $this->render('publications/updatePost.html.twig', [
        'form' => $form->createView(),
        'publication' => $publication,
        //get the user from the session
        'user' => $user,
    ]);
}
#[Route('/publications/search', name: 'app_posts_search')]
     public function search(Request $request,PublicationRepository $publicationRepository): Response
    {
            // Récupérer le terme de recherche depuis la requête
        $searchTerm = $request->query->get('q');

        // Récupérer les produits correspondant au terme de recherche depuis la base de données
        $publications =$publicationRepository->searchPublicationsByTerm($searchTerm);
  
        

        // Passer les produits filtrés au template Twig
        return $this->render('publications/afficherPublications.html.twig', [
            'publications' => $publications,
            'searchTerm' => $searchTerm,
            'form' => $this->createForm(AddPostType::class)->createView(),
        ]);
    }
   
    #[Route('/add-like/{id}', name: 'add_like')]
    public function addLike($id, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository, SessionInterface $session): RedirectResponse
    {
        // Retrieve the user from the session
        $user = $session->get('user');
        
        // Check if a user is logged in
        if ($user instanceof Users) {
            $publication = $publicationRepository->find($id);
            
            if (!$publication) {
                // Handle the case where the publication is not found
                return $this->redirectToRoute('afficher_publications');
            }
            
            $existingReaction = $entityManager->getRepository(Reactions::class)->findOneBy([
                // Find the reaction by the user ID and the publication
                'idUser' => $user->getIdUser(),
                'publication' => $publication
            ]);

            
            
            if ($existingReaction->getUser()->getIdUser() === $user->getIdUser()) {
                // Create a new reaction
                $reaction = new Reactions();
                // Set the user entity directly from the session
                $reaction->setUser($user);
                $reaction->setPublication($publication); // Set the publication entity
                $reaction->setDAjoutReaction(new \DateTime()); // Set the date of adding the reaction
                // Save the reaction to the database
                $entityManager->persist($reaction);
                $entityManager->flush();
            }
    
            // Redirect back to the publications page
            return $this->redirectToRoute('afficher_publications');
        } else {
            // Handle the case where the user is not logged in
            // You might want to redirect the user to the login page or display an error message
            return $this->redirectToRoute('app_login');
        }
    }

    
}