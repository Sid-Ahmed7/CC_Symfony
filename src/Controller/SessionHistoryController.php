<?php

namespace App\Controller;

use App\Entity\SessionHistory;

use App\Repository\SessionRepository;
use App\Entity\User;
use App\Form\SessionHistoryType;
use App\Repository\SessionHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/session/history')]
final class SessionHistoryController extends AbstractController
{
    // #[Route(name: 'app_session_history_index', methods: ['GET'])]
    // public function index(SessionHistoryRepository $sessionHistoryRepository): Response
    // {
    //     return $this->render('session_history/index.html.twig', [
    //         'session_histories' => $sessionHistoryRepository->findAll(),
    //     ]);
    // }

    #[Route('/user', name: 'app_session_history_user', methods: ['GET'])]
    public function getHistory(SessionHistoryRepository $sessionHistoryRepository): Response
{
    $user = $this->getUser();
    $sessionHistories = $sessionHistoryRepository->findBy(['member' => $user]);

    return $this->render('session_history/index.html.twig', [
        'session_histories' => $sessionHistories,
    ]);
}

    #[Route('/{id}', name: 'app_session_history_show', methods: ['GET'])]
    public function show(SessionHistory $sessionHistory): Response
    {
        return $this->render('session_history/show.html.twig', [
            'session_history' => $sessionHistory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionHistoryType::class, $sessionHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_session_history_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session_history/edit.html.twig', [
            'session_history' => $sessionHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_history_delete', methods: ['POST'])]
    public function delete(Request $request, SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sessionHistory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sessionHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session_history_user', [], Response::HTTP_SEE_OTHER);
    }
}
