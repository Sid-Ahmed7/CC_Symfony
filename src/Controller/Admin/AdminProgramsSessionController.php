<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Enum\UserAccountStatusEnum; 
use App\Form\Admin\AdminCoachType;
use App\Form\Admin\AdminUserType;
use App\Form\Admin\AdminSessionType;
use App\Form\Admin\AdminProgramType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FileUploader;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminProgramsSessionController extends AbstractController
{
    #[Route('/gestion/programs', name: 'app_admin_programs') ]
    public function gestionPrograms(
        EntityManagerInterface $entityManager, 
        ProgramRepository $programRepository, 
        SessionRepository $sessionRepository,
        Request $request
    ): Response {

        $admin = $this->getUser();
        
        $pageSize = 5;
    
        $currentPage = $request->query->getInt('page', 1);
        $currentPageSession = $request->query->getInt('pageSession', 1);
        
        $offset = ($currentPage - 1) * $pageSize;
        $offsetSession = ($currentPageSession - 1) * $pageSize;

        $programs = $programRepository->findBy([], null, $pageSize, $offset);
        $sessions = $sessionRepository->findBy([], null, $pageSize, $offsetSession);

        $totalPrograms = $programRepository->count([]);
        $totalPages = ceil($totalPrograms / $pageSize);

        $totalSessions = $sessionRepository->count([]);
        $totalPagesSession = ceil($totalSessions / $pageSize);


        return $this->render('admin/admin_programs.html.twig', [
            'programs' => $programs,
            'sessions' => $sessions,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'currentPageSession' => $currentPageSession,
            'totalPagesSession' => $totalPagesSession,
        ]);
    }

    #[Route('/gestion/programs/new', name: 'app_admin_new_program', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/form/program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/gestion/programs/edit/{id}', name: 'app_admin_program_edit', methods: ['GET', 'POST'])]
    public function editProgram(Request $request, EntityManagerInterface $entityManager, Program $program, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AdminProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('coverImage')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/program_pictures');
                $program->setCoverImage($newFilename);
            }
            $entityManager->persist($program);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/form/program/edit.html.twig', [
            'form' => $form,
            'program' => $program,
        ]);
    }

    #[Route('/gestion/programs/delete/{id}', name: 'app_admin_program_delete', methods: ['POST'])]
    public function deleteProgram(Request $request,Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $reviews = $program->getReviews();

            foreach ($reviews as $review) {
                $entityManager->remove($review);
            }

            foreach ($program->getSessions() as $session) {
                $sessionHistories = $session->getSessionHistories();
                foreach ($sessionHistories as $history) {
                    $entityManager->remove($history); 
                }
    
                $entityManager->remove($session); 
            }
    
         
           
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
    }

    //Session 

    #[Route('/gestion/session/new', name: 'app_admin_new_session', methods: ['GET', 'POST'])]
    public function addSession(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programs = $entityManager->getRepository(Program::class)->findAll();

        $session = new Session();
        
        $form = $this->createForm(AdminSessionType::class, $session, [
            'programs' => $programs, 
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->setProgram($form->get('program')->getData());
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/form/session/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/gestion/session/edit/{id}', name: 'app_admin_session_edit', methods: ['GET', 'POST'])]
    public function editSession(Request $request,Session $session, EntityManagerInterface $entityManager): Response
    {

        $programs = $entityManager->getRepository(Program::class)->findAll();

        $form = $this->createForm(AdminSessionType::class, $session, [
            'programs' => $programs,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session); 
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/form/session/edit.html.twig',[
            'form' => $form,
        ]);
    }

    #[Route('/gestion/session/delete/{id}', name: 'app_admin_session_delete')]
    public function deleteSession(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->get('_token'))) {

            foreach ($session->getSessionHistories() as $history) {
                $entityManager->remove($history); 
            }

            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_programs', [], Response::HTTP_SEE_OTHER);
    }

}