<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Entity\Program;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); 

        if (!$user) {
            $programs = $entityManager->getRepository(Program::class)
                ->createQueryBuilder('p')
                ->leftJoin('p.reviews', 'r') 
                ->groupBy('p.id')
                ->orderBy('AVG(r.rating)', 'DESC') 
                ->setMaxResults(5) 
                ->getQuery()
                ->getResult();

            
            $coaches = $entityManager->getRepository(Coach::class)
                ->findAll();

            return $this->render('index.html.twig', [
                'programs' => $programs,
                'coaches' => $coaches,
            ]);
        }

        if ($user instanceof Coach) {
            $programs = $entityManager->getRepository(Program::class)
                ->createQueryBuilder('p')
                ->where('p.coach = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            $sessions = $entityManager->getRepository(Session::class)
                ->createQueryBuilder('s')
                ->innerJoin('s.program', 'p')
                ->where('p.coach = :user')
                ->setParameter('user', $user)
                ->orderBy('s.date', 'DESC')
                ->getQuery()
                ->getResult();



            return $this->render('coach/index.html.twig', [
                'programs' => $programs,
                'sessions' => $sessions,

            ]);
        }

        if (!$user instanceof Coach) {
            $sessions = $entityManager->getRepository(Session::class)
                ->createQueryBuilder('s')
                ->innerJoin('s.sessionHistories', 'sh')
                ->innerJoin('sh.member', 'm')
                ->where('m = :user')
                ->setParameter('user', $user)
                ->orderBy('s.date', 'DESC')
                ->setMaxResults(2)
                ->getQuery()
                ->getResult();

            return $this->render('user/index.html.twig', [
                'sessions' => $sessions,
            ]);
        }

        return new Response('Page introuvable', 404);
    }
}
