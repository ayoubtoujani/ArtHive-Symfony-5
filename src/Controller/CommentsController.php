<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Users;
use App\Form\AddCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentsController extends AbstractController
{
   
    #[Route('/showComments/{id}', name: 'show_comments')]
    public function addComment($id, Request $request, SessionInterface $session, PublicationRepository $publicationRepository): Response
    {
        // Get the publication entity by ID
        $publication = $publicationRepository->find($id);
    
        // Get comments associated with the publication with the repository of the class commentaires
        $findCommentsBypublicationId = $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['publication' => $publication]);
    // Retrieve the user from the session
    $user = $session->get('user');
        
    // Check if a user is logged in
    if ($user instanceof Users) {
        // Create a new instance of the Commentaires entity
        $comment = new Commentaires();
    
        // Create and handle the comment form
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the authenticated user from the session
           // Fetch the user from the database
           $userRepository = $this->getDoctrine()->getRepository(Users::class);
           $userFromDb = $userRepository->find($user->getIdUser());
    
            // Set the user for the comment
            $comment->setUser($userFromDb);
    
            // Set the date of adding the comment
            $comment->setDAjoutCommentaire(new \DateTime('now'));
    
            // Set the publication for the comment
            $comment->setPublication($publication);
    
            // Save the comment to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Redirect to the comments page after adding the comment
            return $this->redirectToRoute('show_comments', ['id' => $id]);
        }
    
        // If the form is not valid or not submitted, render the comments page with the form
        return $this->render('comments/showPostComments.html.twig', [
            'commentForm' => $form->createView(),
            'publication' => $publication,
            'comments' => $findCommentsBypublicationId,
            'userFromDb' => $user
        ]);
    } else {
        // Handle the case where the user is not logged in
        // You might want to redirect the user to the login page or display an error message
        return $this->redirectToRoute('app_login');
    }
    
}
#[Route('/deleteComment/{id}', name: 'delete_comment')]
public function deleteComment($id, Request $request, SessionInterface $session, EntityManagerInterface $entityManager): RedirectResponse
{
    // Fetch the comment entity from the database
    $commentRepository = $this->getDoctrine()->getRepository(Commentaires::class);
    $comment = $commentRepository->find($id);

    // Check if the comment exists
    if (!$comment) {
        throw $this->createNotFoundException('Comment not found');
    }

    // Retrieve the user from the session
    $user = $session->get('user');
    if (!$user instanceof Users) {
        // Handle case where user is not authenticated
        // Redirect to login page or display an error message
        return $this->redirectToRoute('app_login');
    }

    // Check if the current user is the owner of the comment
    if ($comment->getUser()->getIdUser() === $user->getIdUser()){
        // Delete the comment
        $entityManager->remove($comment);
        $entityManager->flush();

        // Redirect to the comments page after deleting the comment
        return $this->redirectToRoute('show_comments', ['id' => $comment->getPublication()->getIdPublication()]);
   }

    
    // You may want to display an error message or handle this case differently
   // throw new AccessDeniedException('You are not allowed to delete this comment');
    // Redirect to the comments page after deleting the comment
    // return $this->redirectToRoute('show_comments', ['id' => $comment->getPublication()->getIdPublication()]);
}
#[Route('/updateComment/{id}', name: 'update_comment')]
public function updateComment($id, Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    // Fetch the comment entity from the database
    $commentRepository = $this->getDoctrine()->getRepository(Commentaires::class);
    $comment = $commentRepository->find($id);

    // Check if the comment exists
    if (!$comment) {
        throw $this->createNotFoundException('Comment not found');
    }

    // Retrieve the user from the session
    $user = $session->get('user');

    // Check if the user is authenticated
    if (!$user instanceof Users) {
        // Handle case where user is not authenticated
        // Redirect to login page or display an error message
        return $this->redirectToRoute('app_login');
    }

    // Check if the current user is the owner of the comment
    if ($comment->getUser()->getIdUser() === $user->getIdUser()) {
        // Create and handle the comment update form
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated comment to the database
            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirect to the comments page after updating the comment
            return $this->redirectToRoute('show_comments', ['id' => $comment->getPublication()->getIdPublication()]);
        }

        // Render the comment update form
        return $this->render('comments/showPostComments.html.twig', [
            'commentForm' => $form->createView(),
            'comment' => $comment,
            'publication' => $comment->getPublication(),
            'comments' => $this->getDoctrine()->getRepository(Commentaires::class)->findBy(['publication' => $comment->getPublication()]),
            'userFromDb' => $user,

        ]);
    }

    // If the user is not the owner of the comment, deny access
    throw new AccessDeniedException('You are not allowed to update this comment');
}

}