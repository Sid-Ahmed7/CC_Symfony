<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, ProgramRepository $programRepository, CoachRepository $coachRepository, SessionRepository $sessionRepository): Response
    {
        $user = $this->getUser(); 

        if (!$user) {
            $programs = $programRepository->findTopRatedPrograms(5);
            $coaches = $coachRepository->findAll();

            return $this->render('index.html.twig', [
                'programs' => $programs,
                'coaches' => $coaches,
            ]);
        }

        if ($user instanceof Coach) {
            $programs = $programRepository->findProgramsByCoach($user);
            $sessions = $sessionRepository->findSessionsByCoach($user);

            return $this->render('coach/index.html.twig', [
                'programs' => $programs,
                'sessions' => $sessions,

            ]);
        }

        if (!$user instanceof Coach) {
            $sessions = $sessionRepository->findSessionsByUser($user, 2);
            $programs = $programRepository->findLastProgramsByUser($user);


            return $this->render('user/index.html.twig', [
                'programs' => $programs,
                'sessions' => $sessions,
                'user' => $user,
            ]);
        }

        return new Response('Page introuvable', 404);
    }
}
