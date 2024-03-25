<?php

namespace App\Controller;
use App\Entity\Users;
use App\Entity\Reactions;
use App\Entity\Publications;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    
   /* #[Route('/like/{id}', name: 'like_publication', methods: ['POST'])] */
    public function likePublication(Request $request, Publications $publication): JsonResponse
{
    // Check if user is logged in
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'User not logged in'], 401);
    }

    // Get the EntityManager
    $entityManager = $this->getDoctrine()->getManager();

    // Save like to database
    $reaction = new Reactions();
    $reaction->setUser($this->getUserIdFromSession());  // Set the user directly
    $reaction->setPublication($publication); // Set the publication directly
    $entityManager->persist($reaction);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Liked successfully']);
}
   

    /*#[Route('/unlike/{id}', name: 'unlike_publication', methods: ['POST'])] */
    public function unlikePublication(Request $request, Publications $publication): JsonResponse
{
    // Check if user is logged in
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'User not logged in'], 401);
    }

    // Get the EntityManager
    $entityManager = $this->getDoctrine()->getManager();

    // Find the reaction by user ID and publication ID
    $reaction = $entityManager->getRepository(Reactions::class)->findOneBy([
        'user' => $this->getUserIdFromSession(),
        'publication' => $publication
    ]);

    // If reaction found, remove it
    if ($reaction) {
        $entityManager->remove($reaction);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Unliked successfully']);
    }

    // If reaction not found
    return new JsonResponse(['message' => 'Reaction not found'], 404);
}

    // Helper method to get the whole user from session
    private function getUserIdFromSession(): Users
    {
        $user = $this->getUser();
        if ($user instanceof Users) {
            return $user;
        }
        throw new \Exception('User not found');
    }
}
