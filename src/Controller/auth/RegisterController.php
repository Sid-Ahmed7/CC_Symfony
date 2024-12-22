<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegisterType;
use App\Enum\UserAccountStatusEnum;
use App\Event\PasswordHasherListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPlainPassword($form->get('password')->getData());
            $randomNumber = rand(1, 100); 
            $user->setProfilePicture("https://picsum.photos/400/550?random=$randomNumber");
            $user->setRoles(['ROLE_USER']);
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('register/index.html.twig', [
            'form' => $form,
        ]);
    }
}