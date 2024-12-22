<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Coach;
use App\Repository\UserRepository;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, CoachRepository $coachRepository): Response
    {
        $users = $userRepository->findLatestUsers(5);
        $coachs = $coachRepository->findLatestCoaches(5);

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'coachs' => $coachs,
        ]);
    }
}
