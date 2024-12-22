<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Coach;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\SessionHistoryRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route( name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, CoachRepository $coachRepository, ProgramRepository $programRepository, SessionRepository $sessionRepository): Response
    {
        $admin = $this->getUser();
        $users = $userRepository->findLatestUsers(5);
        $coachs = $coachRepository->findLatestCoaches(5);
        $programs = $programRepository->findLatestPrograms(5);
        $sessions = $sessionRepository->findLatestSessions(5);
        return $this->render('admin/admin_dashboard.html.twig', [
            'admin' => $admin,
            'users' => $users,
            'coachs' => $coachs,
            'programs' => $programs,
            'sessions' => $sessions,
        ]);
    }


    #[Route('/gestion/users', name: 'app_admin_users')]
    public function gestionUsers(
        EntityManagerInterface $entityManager, 
        UserRepository $userRepository, 
        CoachRepository $coachRepository, 
        Request $request
    ): Response {

        $admin = $this->getUser();
        
        $pageSize = 5;
    
        $currentPage = $request->query->getInt('page', 1);
        $currentPageCoach = $request->query->getInt('pageCoach', 1);
        
        $offset = ($currentPage - 1) * $pageSize;
        $offsetCoach = ($currentPageCoach - 1) * $pageSize;

        $users = $userRepository->findBy([], null, $pageSize, $offset);
    
        $totalUsers = $userRepository->count([]);
        $totalPages = ceil($totalUsers / $pageSize);
    
        $coachs = $coachRepository->findBy([], null, $pageSize, $offsetCoach);
        $totalCoaches = $coachRepository->count([]);
        $totalPagesCoach = ceil($totalCoaches / $pageSize);
    
        return $this->render('admin/admin_users.html.twig', [
            'admin' => $admin,
            'users' => $users,
            'coachs' => $coachs,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'currentPageCoach' => $currentPageCoach,
            'totalPagesCoach' => $totalPagesCoach,
        ]);
    }
   

    #[Route('/gestion/users/new', name: 'app_admin_new_user', methods: ['GET', 'POST'])]
    public function newUser(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPlainPassword($form->get('password')->getData());
            $uploadedFile = $form->get('profilePicture')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $user->setProfilePicture($newFilename);
            }
            $user->setRoles(['ROLE_USER']);
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/form/users/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/gestion/users/edit/{id}', name: 'app_admin_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, EntityManagerInterface $entityManager, User $user, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('profilePicture')->getData();
            
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $user->setProfilePicture($newFilename);
            }
            $entityManager->persist(object: $user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('admin/form/users/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/gestion/users/delete/{id}', name: 'app_admin_delete_user', methods: ['POST'])]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            
            foreach ($user->getPrograms() as $program) {
                $user->removeProgram($program); 
            }
    
            foreach ($user->getCoachs() as $coach) {
                $user->removeCoach($coach); 
            }
    
            foreach ($user->getSessions() as $session) {
                $user->removeSession($session);
            }
    
            foreach ($user->getSessionHistories() as $sessionHistory) {
                $entityManager->remove($sessionHistory); 
            }
    
            foreach ($user->getReviews() as $review) {
                $entityManager->remove($review); 
            }
    
            $entityManager->remove($user);
            
            $entityManager->flush(); 
        }
    
        return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/gestion/coachs/new', name: 'app_admin_new_coach', methods: ['GET', 'POST'])]
    public function newCoach(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $coach = new Coach();
        $form = $this->createForm(AdminCoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coach->setPlainPassword($form->get('password')->getData());
            $uploadedFile = $form->get('profilePicture')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $coach->setProfilePicture($newFilename);
            }
            $coach->setRoles(['ROLE_COACH']);
            $entityManager->persist($coach);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/form/coach/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/gestion/coachs/edit/{id}', name: 'app_admin_edit_coach', methods: ['GET', 'POST'])]
    public function editCoach(Request $request, EntityManagerInterface $entityManager, Coach $coach,FileUploader $fileUploader): Response
    {
        $form = $this->createForm(AdminCoachType::class, $coach, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
            $coach->setPlainPassword($plainPassword);
            }
            $uploadedFile = $form->get('profilePicture')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $coach->setProfilePicture($newFilename);
            }
    
            $entityManager->persist($coach);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/form/coach/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/gestion/coachs/delete/{id}', name: 'app_admin_delete_coach', methods: ['POST'])]
    public function deleteCoach(EntityManagerInterface $entityManager, Coach $coach): Response
    {
        foreach ($coach->getPrograms() as $program) {
            foreach ($program->getReviews() as $review) {
                $entityManager->remove($review); 
            }
    
           
            foreach ($program->getSessions() as $session) {
                foreach ($session->getSessionHistories() as $history) {
                    $entityManager->remove($history);  
                }
    
                $entityManager->remove($session);  
            }
    
            $entityManager->remove($program);
        }
    
        
        foreach ($coach->getSessions() as $session) {
            
            foreach ($session->getSessionHistories() as $history) {
                $entityManager->remove($history);  
            }
    
            $entityManager->remove($session); 
        }
    
        
        $entityManager->remove($coach);  
        $entityManager->flush();  
    
        return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
    }

    

}