<?php

namespace App\Controller\Auth;

use App\Entity\Coach;
use App\Form\RegisterCoachType;
use App\Enum\UserAccountStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Event\PasswordHasherListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterCoachController extends AbstractController
{
    #[Route('/register/coach', name: 'app_register_coach')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $coach = new Coach();
        $form = $this->createForm(RegisterCoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coach->setPlainPassword($form->get('password')->getData());
            $coach->setRoles(['ROLE_COACH']);
            $randomNumber = rand(1, 100); 
            $coach->setProfilePicture("https://picsum.photos/400/550?random=$randomNumber");
            $entityManager->persist($coach);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('register_coach/index.html.twig', [
            'form' => $form,
        ]);
    }
}