<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\Coach;
use App\Form\ProgramType;
use App\Form\SessionType;
use App\Service\FileUploader;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/coach')]
#[IsGranted('ROLE_COACH')]
class CoachController extends AbstractController
{
    #[Route('/program/show', name: 'app_program_show_all', methods: ['GET'])]
    public function showAddProgram(ProgramRepository $programRepository, Sessionrepository $sessionRepository): Response
    {
        $coach = $this->getUser();


        $programs = $programRepository->findBy(['coach' => $coach]);
    
        $sessions = $sessionRepository->findBy(['coach' => $coach]);
    
        return $this->render('coach/coach_show_all.html.twig', [
            'programs' => $programs,
            'sessions' => $sessions,
        ]);
    }
    
    #[Route('/program/new', name: 'app_program_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader
    ): Response {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $program->setCoach($this->getUser());
            $program->setCreatedAt(new \DateTimeImmutable());
            $uploadedFile = $form->get('coverImage')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/program_pictures');
                $program->setCoverImage($newFilename);
            }
   
            $entityManager->persist($program);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('program/new.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/program/{id}/edit', name: 'app_program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('coverImage')->getData();

            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/program_pictures');
                $program->setCoverImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/program/{id}', name: 'app_program_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/session/new', name: 'app_session_new', methods: ['GET', 'POST'])]
    public function newSession(Request $request, EntityManagerInterface $entityManager): Response
    {
        $coach = $this->getUser();
        $programs = $entityManager->getRepository(Program::class)->findBy(['coach' => $coach]);

        $session = new Session();

        $form = $this->createForm(SessionType::class, $session, [
            'programs' => $programs,
           
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->setCoach($coach);
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('app_program_show_all', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_edit', methods: ['GET', 'POST'])]
    public function editSession(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $coach = $this->getUser();
        $programs = $entityManager->getRepository(Program::class)->findBy(['coach' => $coach]);
        $form = $this->createForm(SessionType::class, $session, [
            'programs' => $programs,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_session_delete', methods: ['POST'])]
    public function deleteSession(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session_index', [], Response::HTTP_SEE_OTHER);
    }

}