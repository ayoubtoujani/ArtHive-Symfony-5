<?php

namespace App\Controller;
use App\Entity\Users;
use App\Entity\Publications;
use App\Form\AddPostType;
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
#[Route('/update-post', name: 'update_post', methods: ['GET', 'POST'])]
public function updatePost(Request $request, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository): Response
{
    // Retrieve the publication ID from the request parameters
    $publicationId = $request->request->get('publication_id');

    // Fetch the publication entity based on the retrieved ID
    $publication = $publicationRepository->find($publicationId);

    if (!$publication) {
        // Handle case where publication is not found
        // You can redirect or display an error message
        return $this->redirectToRoute('my_publications');
    }

    // Render the updatePost.html.twig template with the publication entity
    return $this->render('publications/updatePost.html.twig', [
        'publication' => $publication,
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
}