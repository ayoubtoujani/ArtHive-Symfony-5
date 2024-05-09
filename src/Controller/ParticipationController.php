<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Form\ParticipationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Evenements;
use App\Entity\Users;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use App\Repository\ParticipationRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Importez la classe SessionInterface


class ParticipationController extends AbstractController
{
 
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


  


    #[Route('/participations/new', name: 'participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

 

  
    #[Route('/participations/{id}', name: 'participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participation_index', [], Response::HTTP_SEE_OTHER);
    }
}
