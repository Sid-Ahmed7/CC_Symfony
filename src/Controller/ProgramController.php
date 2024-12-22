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
            return $this->redirectToRoute('app_login');
        }
    
        $programs = $user->getPrograms();
    
        $programsWithSessions = [];
        foreach ($programs as $program) {
            $sessionsWithStatus = []; 
            foreach ($program->getSessions() as $session) {
                $isChosen = false;
                
                
                $sessionHistory = $sessionHistoryRepo->findOneBy(['member' => $user, 'session' => $session]);
                
                if ($sessionHistory) {
                    $isChosen = true; 
                }
                
                $sessionsWithStatus[] = [
                    'session' => $session,
                    'isChosen' => $isChosen, 
                ];
            }
    
            $programsWithSessions[] = [
                'program' => $program,
                'sessions' => $sessionsWithStatus, 
            ];
        }
    
        return $this->render('user/programs.html.twig', [
            'programsWithSessions' => $programsWithSessions, 
        ]);
    }



#[Route('/program/{id}', name: 'app_program_show', methods: ['GET'])]
public function showProgram(int $id, ProgramRepository $programRepository): Response
{
    $program = $programRepository->find($id);

    if (!$program) {
        $this->addFlash('danger', 'Le programme n\'existe pas.');
        return $this->redirectToRoute(route: 'app_program_index');
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
    #[IsGranted(attribute: 'ROLE_USER')]
    public function addProgram(
        ProgramRepository $programRepo, 
        EntityManagerInterface $entityManager, 
        $id, 
        Request $request
    ): Response {

        $program = $programRepo->find($id);
    

        if (!$program) {
            $this->addFlash('danger', 'Le programme n\'existe pas.');
            return $this->redirectToRoute(route: 'app_program_index');

        }
    
        $user = $this->getUser();

        if (!$user->getPrograms()->contains($program)) {

            $user->addProgram($program);
    
            $entityManager->flush();
        }
    
     
        return $this->redirectToRoute(route: 'app_user_programs');
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
            $this->addFlash('danger', 'Le programme n\'existe pas.');
            return $this->redirectToRoute(route: 'app_program_index');


        }
    
        $user = $this->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
      
        if (!$user->getPrograms()->contains($program)) {
            throw $this->createAccessDeniedException('Vous devez être inscrit à ce programme pour choisir une séance.');
        }
    
        
        $sessions = $sessionRepo->findBy(['program' => $program]);
    
        
        $availableSessions = array_filter($sessions, function (Session $session) use ($user) {
            foreach ($session->getSessionHistories() as $history) {
                if ($history->getMember() === $user) {
                    return false;
                }
            }
            return true; 
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
            $this->addFlash('danger', 'Le programme n\'existe pas.');
            return $this->redirectToRoute(route: 'app_program_index');


        }
    
       
        $session = $sessionRepo->find($sessionId);
    
        
        if (!$session) {
            $this->addFlash('danger', 'La session n\'existe pas.');

        }
    
        
        $user = $this->getUser();
    
       
        if (!$user) {
            return $this->redirectToRoute('app_login');
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
        $entityManager->persist($session); 
        $entityManager->flush();
       
        return $this->redirectToRoute('app_home');
    }

    #[Route('/program/{id}/cancel', name: 'program_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Request $request, Program $program, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
    
  
        if (!$program->getUsers()->contains($user)) {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à ce programme.');
            return $this->redirectToRoute('app_user_programs');
        }
    

        foreach ($program->getSessions() as $session) {

            $sessionHistories = $session->getSessionHistories(); 
            foreach ($sessionHistories as $sessionHistory) {
                if ($sessionHistory->getMember() === $user) {
                    $entityManager->remove($sessionHistory); 
                }
            }
        }
    
        $program->removeUser($user);
    
        foreach ($program->getSessions() as $session) {
            if ($session->getMembers()->contains($user)) {
                $session->removeMember($user);
            }
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre participation au programme a été annulée.');
    
        return $this->redirectToRoute('app_user_programs');
    }
}
    

