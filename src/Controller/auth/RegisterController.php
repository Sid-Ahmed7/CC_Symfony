<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegisterType;
use App\Enum\UserAccountStatusEnum;
use App\Repository\UserRepository;
use App\Event\PasswordHasherListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FileUploader;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail(); 

            $existingUser = $userRepository->findOneBy(['email' => $email]);

            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');

                return $this->redirectToRoute('app_register');
            }

            $user->setPlainPassword($form->get('password')->getData());
            $user->setRoles(['ROLE_USER']);
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);

            
            $uploadedFile = $form->get('profilePicture')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $user->setProfilePicture($newFilename);
            }

            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form,
        ]);
    }

}