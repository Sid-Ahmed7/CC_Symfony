<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\SessionHistory;

use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\SessionHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProgramController extends AbstractController
{
    #[Route('/program', name: 'app_program_index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): Response
    {
        $user = $this->getUser();

        $allPrograms = $programRepository->findAll();
     
        $programsWithStatus = [];
    
        foreach ($allPrograms as $program) {
            $isChosen = $user && $user->getPrograms()->contains($program);
            $programsWithStatus[] = [
                'program' => $program,
                'isChosen' => $isChosen,
            ];
        }
    
        return $this->render('program/show_programs.html.twig', [
            'programs' => $programsWithStatus,
        ]);
    }

    #[Route('/user/programs', name: 'app_user_programs', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserPrograms(SessionHistoryRepository $sessionHistoryRepo): Response
    {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
    
        $programs = $user->getPrograms();
    
        // Créer un tableau avec l'information des programmes et des sessions
        $programsWithSessions = [];
        foreach ($programs as $program) {
            $sessionsWithStatus = []; // Tableau des sessions et si l'utilisateur a choisi une session
            foreach ($program->getSessions() as $session) {
                $isChosen = false;
                
                // Vérifie si un historique de session existe pour l'utilisateur et cette session
                $sessionHistory = $sessionHistoryRepo->findOneBy(['member' => $user, 'session' => $session]);
                
                if ($sessionHistory) {
                    $isChosen = true; // L'utilisateur a choisi cette session s'il existe une entrée dans SessionHistory
                }
                
                $sessionsWithStatus[] = [
                    'session' => $session,
                    'isChosen' => $isChosen, // Indique si l'utilisateur a choisi cette séance
                ];
            }
    
            $programsWithSessions[] = [
                'program' => $program,
                'sessions' => $sessionsWithStatus, // Ajoute les sessions associées
            ];
        }
    
        return $this->render('user/programs.html.twig', [
            'programsWithSessions' => $programsWithSessions, // Transmet le tableau avec programmes et sessions
        ]);
    }



#[Route('/program/{id}', name: 'app_program_show', methods: ['GET'])]
public function showProgram(int $id, ProgramRepository $programRepository): Response
{
    $program = $programRepository->find($id);

    if (!$program) {
        throw $this->createNotFoundException('Le programme n\'existe pas.');
    }

    $user = $this->getUser();
    
    $isChosen = false;
    if ($user) {
        $isChosen = $user->getPrograms()->contains($program);
    }

    return $this->render('program/show_detail_program.html.twig', [
        'program' => $program,
        'isChosen' => $isChosen,
    ]);
}

    #[Route('/program/{id}/add', name: 'app_program_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addProgram(
        ProgramRepository $programRepo, 
        EntityManagerInterface $entityManager, 
        $id, 
        Request $request
    ): Response {

        $program = $programRepo->find($id);
    

        if (!$program) {
            throw $this->createNotFoundException('Le programme n\'existe pas.');
        }
    
        $user = $this->getUser();

        if (!$user->getPrograms()->contains($program)) {

            $user->addProgram($program);
    
            $entityManager->flush();
        }
    
     
        return $this->redirectToRoute('app_user_programs');
    }

 
    #[Route('/program/{id}/sessions/choose', name: 'app_session_choose', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function chooseSession(
        Request $request,
        ProgramRepository $programRepo,
        SessionRepository $sessionRepo,
        $id
    ): Response {
       
        $program = $programRepo->find($id);
    
        if (!$program) {
            throw $this->createNotFoundException('Le programme n\'existe pas.');
        }
    
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour choisir une session.');
        }
    
        // Vérifier si l'utilisateur est inscrit au programme
        if (!$user->getPrograms()->contains($program)) {
            throw $this->createAccessDeniedException('Vous devez être inscrit à ce programme pour choisir une séance.');
        }
    
        // Récupérer les séances disponibles pour ce programme
        $sessions = $sessionRepo->findBy(['program' => $program]);
    
        // Filtrer les séances déjà rejointes par l'utilisateur
        $availableSessions = array_filter($sessions, function (Session $session) use ($user) {
            foreach ($session->getSessionHistories() as $history) {
                if ($history->getMember() === $user) {
                    return false; // Si l'utilisateur a déjà rejoint cette séance, elle n'est pas disponible
                }
            }
            return true; // Si l'utilisateur n'a pas rejoint la séance, elle est disponible
        });
    
        return $this->render('session/select_session.html.twig', [
            'program' => $program,
            'sessions' => $availableSessions, 
        ]);
    }
    
    #[Route('/program/{programId}/session/{sessionId}/join', name: 'app_session_join', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function joinSession(
        ProgramRepository $programRepo,
        SessionRepository $sessionRepo,
        SessionHistoryRepository $sessionHistoryRepo,  
        EntityManagerInterface $entityManager,
        $programId,
        $sessionId
    ): Response {
       
        $program = $programRepo->find($programId);
    
       
        if (!$program) {
            throw $this->createNotFoundException('Le programme n\'existe pas.');
        }
    
       
        $session = $sessionRepo->find($sessionId);
    
        
        if (!$session) {
            throw $this->createNotFoundException('La session n\'existe pas.');
        }
    
        
        $user = $this->getUser();
    
       
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour rejoindre une session.');
        }
    
        
        $existingHistory = $sessionHistoryRepo->findOneBy([
            'member' => $user,
            'session' => $session
        ]);
    
        if (!$existingHistory) {
          
            $sessionHistory = new SessionHistory();
            $sessionHistory->setMember($user);
            $sessionHistory->setSession($session);
            $sessionHistory->setSessionDate(new \DateTimeImmutable());  
            $sessionHistory->setGoals(''); 
            $sessionHistory->setComments('');
    
            $entityManager->persist($sessionHistory);
            $entityManager->flush();  
        }
        $entityManager->persist($session); // Mettez à jour la relation dans Doctrine
        $entityManager->flush();
       
        return $this->redirectToRoute('app_home');
    }

    #[Route('/program/{id}/cancel', name: 'program_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Request $request, Program $program, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser(); // Récupère l'utilisateur actuel
    
        // Vérifier si l'utilisateur est inscrit au programme
        if (!$program->getUsers()->contains($user)) {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à ce programme.');
            return $this->redirectToRoute('app_user_programs'); // Redirection vers la liste des programmes
        }
    
        // Annuler la participation de l'utilisateur au programme
        $program->removeUser($user);
    
        // Annuler les séances associées à ce programme pour cet utilisateur
        foreach ($program->getSessions() as $session) {
            if ($session->getMembers()->contains($user)) {
                $session->removeMember($user); // Retirer l'utilisateur de la séance
                // Ne supprimez pas la séance, elle peut être réutilisée pour un autre utilisateur
            }
        }
    
        // Sauvegarder les modifications
        $entityManager->flush();
    
        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Votre participation au programme a été annulée.');
    
        // Rediriger vers la liste des programmes de l'utilisateur
        return $this->redirectToRoute('app_user_programs');
    }
}
    

