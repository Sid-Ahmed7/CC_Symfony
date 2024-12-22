<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use App\Entity\SessionHistory;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\SessionHistoryRepository;
use App\Enum\UserAccountStatusEnum; 
use App\Form\Admin\AdminCoachType;
use App\Form\Admin\AdminUserType;
use App\Form\Admin\AdminSessionType;
use App\Form\Admin\AdminProgramType;
use App\Form\Admin\AdminSessionHistoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\FileUploader;

class AdminProgramsSessionController extends AbstractController
{
    #[Route('/admin/gestion/programs', name: 'app_admin_programs')]
    public function gestionPrograms(
        EntityManagerInterface $entityManager, 
        ProgramRepository $programRepository, 
        SessionRepository $sessionRepository,
        SessionHistoryRepository $sessionHistoryRepository,
        Request $request
    ): Response {

        $admin = $this->getUser();
        
        $pageSize = 5;
    
        $currentPage = $request->query->getInt('page', 1);
        $currentPageSession = $request->query->getInt('pageSession', 1);
        $currentPageSessionHistory = $request->query->getInt('pageSessionHistory', 1);
        
        $offset = ($currentPage - 1) * $pageSize;
        $offsetSession = ($currentPageSession - 1) * $pageSize;
        $offsetSessionHistory = ($currentPageSessionHistory - 1) * $pageSize;

        $programs = $programRepository->findBy([], null, $pageSize, $offset);
        $sessions = $sessionRepository->findBy([], null, $pageSize, $offsetSession);
        $sessionHistories = $sessionHistoryRepository->findBy([], null, $pageSize, $offsetSessionHistory);

        $totalPrograms = $programRepository->count([]);
        $totalPages = ceil($totalPrograms / $pageSize);

        $totalSessions = $sessionRepository->count([]);
        $totalPagesSession = ceil($totalSessions / $pageSize);

        $totalSessionHistories = $sessionHistoryRepository->count([]);
        $totalPagesSessionHistories = ceil($totalSessionHistories / $pageSize);
        return $this->render('admin/admin_programs.html.twig', [
            'programs' => $programs,
            'sessions' => $sessions,
            'sessionHistories' => $sessionHistories,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'currentPageSession' => $currentPageSession,
            'totalPagesSession' => $totalPagesSession,
            'currentPageSessionHistory' => $currentPageSessionHistory,
            'totalPagesSessionHistories' => $totalPagesSessionHistories,
        ]);
    }

//Add a new Program
#[Route('/admin/gestion/programs/new', name: 'app_admin_new_program')]
public function newProgram(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
{
    $program = new Program();
    $form = $this->createForm(AdminProgramType::class, $program);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $program->setCreatedAt(new \DateTimeImmutable());
        $uploadedFile = $form->get('coverImage')->getData();
        if ($uploadedFile) {
            $newFilename = $fileUploader->upload($uploadedFile, '/program_pictures');
            $program->setCoverImage($newFilename);
        }
        $entityManager->persist($program);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_programs');
    }

    return $this->render('admin/form/program/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
//Edit a Program
#[Route('/admin/gestion/programs/edit/{id}', name: 'app_admin_edit_program')]
public function editProgram(Request $request, EntityManagerInterface $entityManager, Program $program, FileUploader $fileUploader): Response
{
    $form = $this->createForm(ProgramType::class, $program);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $uploadedFile = $form->get('programPicture')->getData();
        if ($uploadedFile) {
            $newFilename = $fileUploader->upload($uploadedFile, '/program_pictures');
            $program->setProgramPicture($newFilename);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_programs_index');
    }
    return $this->render('admin/edit_program.html.twig', [
        'form' => $form->createView(),
        'program' => $program,
    ]);
}
//Delete a Program
#[Route('/admin/gestion/programs/delete/{id}', name: 'app_admin_delete_program')]
public function deleteProgram(Program $program, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($program);
    $entityManager->flush();

    return $this->redirectToRoute('app_programs_index');
}

//Add Session
#[Route('/admin/gestion/programs/add_session/{id}', name: 'app_admin_add_session')]
public function addSession(Program $program, EntityManagerInterface $entityManager): Response
{
    $session = new Session();
    $session->setProgram($program);
    $entityManager->persist($session);
    $entityManager->flush();

    return $this->redirectToRoute('app_programs_index');
}
//Edit Session
#[Route('/admin/gestion/programs/edit_session/{id}', name: 'app_admin_edit_session')]
public function editSession(Session $session, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(SessionType::class, $session);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_programs_index');
    }
    return $this->render('admin/programs/edit_session.html.twig', [
        'form' => $form->createView(),
    ]);
}
//Delete Session
#[Route('/admin/gestion/programs/delete_session/{id}', name: 'app_admin_delete_session')]
public function deleteSession(Session $session, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($session);
    $entityManager->flush();

    return $this->redirectToRoute('app_programs_index');


}

//Add SessionHistory

#[Route('/admin/gestion/programs/add_session_history/{id}', name: 'app_admin_add_session_history')]
public function addSessionHistory(Program $program, EntityManagerInterface $entityManager): Response
{
    $session = new Session();
    $session->setProgram($program);
    $entityManager->persist($session);
    $entityManager->flush();

    return $this->redirectToRoute('app_programs_index');    
}
//Edit SessionHistory
#[Route('/admin/gestion/programs/edit_session_history/{id}', name: 'app_admin_edit_session_history')]
public function editSessionHistory(SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(SessionHistoryType::class, $sessionHistory);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        return $this->redirectToRoute('app_programs_index');
    }
    return $this->render('admin/programs/edit_session_history.html.twig', [
        'form' => $form->createView(),
    ]);
}
//Delete SessionHistory
#[Route('/admin/gestion/programs/delete_session_history/{id}', name: 'app_admin_delete_session_history')]
public function deleteSessionHistory(SessionHistory $sessionHistory, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($sessionHistory);
    $entityManager->flush();

    return $this->redirectToRoute('app_programs_index');
}
}