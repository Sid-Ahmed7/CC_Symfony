<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Coach;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $coachs = $entityManager->getRepository(Coach::class)
            ->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'coachs' => $coachs,
        ]);
    }
}
