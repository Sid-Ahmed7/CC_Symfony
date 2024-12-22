<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoachRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Coach;
use App\Entity\User;

class ResetController extends AbstractController
{
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
        string $token, 
        Request $request, 
        CoachRepository $coachRepository, 
        UserRepository $userRepository, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $coach = $coachRepository->findOneBy(['resetPasswordToken' => $token]);
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);

        
        if ($coach) {
            return $this->resetPassword($coach, $request, $entityManager, $passwordHasher);
        }
        
        if ($user) {
            return $this->resetPassword($user, $request, $entityManager, $passwordHasher);
        }

        
        $this->addFlash('error', 'Token invalide');
        return $this->redirectToRoute('app_forgot_password');
    }

    private function resetPassword($entity, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $newPassword = $request->get('_password');
            $confirmPassword = $request->get('_repeat-password');

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('app_reset_password', ['token' => $request->get('token')]);
            }


            $entity->setPlainPassword($newPassword);
            $entity->setResetToken(""); 

            $entityManager->persist($entity);
            $entityManager->flush();



            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset/reset.html.twig', [
            'resetToken' => $request->get('token'),
            'entity' => $entity
        ]);
    }
}
