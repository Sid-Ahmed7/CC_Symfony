<?php

namespace App\Controller\Auth;

use App\Entity\Coach;
use App\Form\RegisterCoachType;
use App\Enum\UserAccountStatusEnum;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Event\PasswordHasherListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FileUploader;


class RegisterCoachController extends AbstractController
{
    #[Route('/register/coach', name: 'app_register_coach')]
    public function register(Request $request,  FileUploader $fileUploader, EntityManagerInterface $entityManager, CoachRepository $coachRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $coach = new Coach();
        $form = $this->createForm(RegisterCoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $coach->getEmail();
            $existingCoach = $coachRepository->findOneBy(['email' => $email]);
            if ($existingCoach) {
                $this->addFlash('error', 'Un coach avec cet email existe déjà.');

                return $this->redirectToRoute('app_register_coach');
            }

            $coach->setPlainPassword($form->get('password')->getData());
            $coach->setRoles(['ROLE_COACH']);
            $uploadedFile = $form->get('profilePicture')->getData();
            
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $coach->setProfilePicture($newFilename);
            }
            $entityManager->persist($coach);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('register_coach/index.html.twig', [
            'form' => $form,
        ]);
    }
}