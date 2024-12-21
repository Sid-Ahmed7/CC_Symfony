<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\SessionHistory;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/session')]
final class SessionController extends AbstractController
{
    #[Route(name: 'app_session_index', methods: ['GET'])]
    public function index(SessionRepository $sessionRepository): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_session_show', methods: ['GET'])]
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }
    
 
// ------------------------------------------------------------------------------------------
        // Manage session history

    #[Route('/history/new', name: 'app_session_history_new', methods: ['GET', 'POST'])]
    public function addHistory(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $sessionHistory = new SessionHistory();
        $sessionHistory->setSession($session);
        $form = $this->createForm(SessionHistoryType::class, $sessionHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sessionHistory);
            $entityManager->flush();

            // return $this->redirectToRoute('app_session_show', ['id' => $session->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session_history/new.html.twig', [
            'form' => $form->createView(),
            'session' => $session,
        ]);
    }
    #[Route('/{sessionId}/history/{historyId}/edit', name: 'app_session_history_edit', methods: ['GET', 'POST'])]
    public function editHistory(Request $request, SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionHistoryType::class, $sessionHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_session_show', ['id' => $sessionHistory->getSession()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session/history/edit.html.twig', [
            'form' => $form->createView(),
            'sessionHistory' => $sessionHistory,
        ]);
    }

    #[Route('/{sessionId}/history/{historyId}/delete', name: 'app_session_history_delete', methods: ['POST'])]
    public function deleteHistory(Request $request, SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sessionHistory->getId(), $request->get('_token'))) {
            $entityManager->remove($sessionHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session_show', ['id' => $sessionHistory->getSession()->getId()], Response::HTTP_SEE_OTHER);
    }

}
