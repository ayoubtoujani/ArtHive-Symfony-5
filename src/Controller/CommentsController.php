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

class CommentsController extends AbstractController
{
    #[Route('/comments', name: 'app_comments')]
    public function comments(): Response
    {
        // Fetch existing comments from the database
        $comments = $this->getDoctrine()->getRepository(Commentaires::class)->findAll();

        // Render the comments template with the existing comments
        return $this->render('comments/comments.html.twig', [
            'comments' => $comments,
            'commentForm' => $this->createForm(AddCommentType::class)->createView(),
        ]);
    }

    #[Route('/add-comment', name: 'app_add_comment')]
    public function addComment(Request $request, SessionInterface $session): Response
    {
        // Create a new instance of the Commentaires entity
        $comment = new Commentaires();
    
        // Create and handle the comment form
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the authenticated user from the session
            $user = $session->get('user');
    
            // Set the user for the comment
            $comment->setUser($user);
    
            // Set the date of adding the comment
            $comment->setDAjoutCommentaire(new \DateTime('now'));
    
            // Save the comment to the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Redirect to the comments page after adding the comment
            return $this->redirectToRoute('app_comments');
        }
    
        // If the form is not valid or not submitted, render the comments page with the form
        return $this->render('comments/comments.html.twig', [
            'commentForm' => $form->createView(),
        ]);
    }
    
}
