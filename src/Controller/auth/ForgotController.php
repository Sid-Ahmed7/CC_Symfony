<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CoachRepository;
use App\Repository\UserRepository;
use App\Entity\Coach;
use App\Entity\User;
use App\Service\Mailer;

class ForgotController extends AbstractController
{
    #[Route('/forgot', name: 'app_forgot_password')]
    public function forgot(Request $request, EntityManagerInterface $entityManager, CoachRepository $coachRepository, UserRepository $userRepository,Mailer $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->get('_email');
            if (empty($email)) {
                $this->addFlash('error', 'Veuillez entrer un email valide.');
                return $this->redirectToRoute('app_forgot');
            }

             $coach = $coachRepository->findOneBy(['email' => $email]);
            if ($coach) {
    
                $resetToken = Uuid::v4()->toRfc4122();
                $coach->setResetToken($resetToken);
                $entityManager->flush();
                $mailer->sendResetPasswordEmail($coach);
                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
                return $this->redirectToRoute('app_login');
            }

            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user) {
     
                $resetToken = Uuid::v4()->toRfc4122();
                $user->setResetToken($resetToken);
                $entityManager->flush();
                $mailer->sendResetPasswordEmail($user);
                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
                return $this->redirectToRoute('app_login');
            }

             $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse email.');
            return $this->redirectToRoute('app_forgot');
}
        
        return $this->render('forgot/forgot.html.twig');
    }

}
