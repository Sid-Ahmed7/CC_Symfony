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

        // Si l'utilisateur n'est ni connecté ni un coach, afficher les programmes les mieux notés et les coachs
        if (!$user) {
            // Récupérer les programmes les mieux notés avec les catégories
            $programs = $entityManager->getRepository(Program::class)
                ->createQueryBuilder('p')
                ->leftJoin('p.reviews', 'r') // Ajouter la jointure avec les avis (reviews)
                ->groupBy('p.id')
                ->orderBy('AVG(r.rating)', 'DESC') // Tri par la note moyenne des avis
                ->setMaxResults(5) // Afficher les 5 programmes les mieux notés
                ->getQuery()
                ->getResult();

            // Récupérer tous les coachs avec leurs spécialisations
            $coaches = $entityManager->getRepository(Coach::class)
                ->findAll();

            // Retourner une vue avec les programmes, coachs et catégories
            return $this->render('index.html.twig', [
                'programs' => $programs,
                'coaches' => $coaches,
            ]);
        }

        // Si l'utilisateur est un coach
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



            // Retourner une vue avec les programmes, sessions, catégories et spécialisations
            return $this->render('coach/index.html.twig', [
                'programs' => $programs,
                'sessions' => $sessions,

            ]);
        }

        // Si l'utilisateur est un membre (non coach), afficher uniquement ses sessions
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

            // Retourner une vue avec les sessions de l'utilisateur
            return $this->render('user/index.html.twig', [
                'sessions' => $sessions,
            ]);
        }

        return new Response('Page introuvable', 404);
    }
}
