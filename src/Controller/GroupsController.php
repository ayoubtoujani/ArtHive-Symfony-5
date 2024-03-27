<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Groups;

class GroupsController extends AbstractController
{
    
     
    #[Route('/groups', name: 'groups_index', methods: ['GET', 'POST'])]
     public function index(): Response
    {
        // Get the Doctrine entity manager
        $entityManager = $this->getDoctrine()->getManager();
        
        // Get all groups from the database
        $groups = $entityManager->getRepository(Groups::class)->findAll();
        
        // Render the Twig template and pass the groups to it
        return $this->render('groups/groups.html.twig', [
            'groups' => $groups,
        ]);
    }
}
