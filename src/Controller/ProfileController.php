<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Coach;
use App\Form\EditUserProfileType;
use App\Service\FileUploader;
use App\Form\EditCoachProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    
    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function userProfile(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/user_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/coach', name: 'app_coach_profile')]
    #[IsGranted('ROLE_COACH')]
    public function coachProfile(): Response
    {
        $coach = $this->getUser();
        $specialities = $coach->getSpecialities();


        if (!$coach) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/coach_profile.html.twig', [
            'coach' => $coach,
            'specialities' => $specialities,
        ]);
    }

    #[Route('/profile/edit/user', name: 'app_edit_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function editUserProfile(Request $request, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(EditUserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $request->get('newPassword');
            $confirmPassword = $request->get('confirmPassword');
            
            if ($newPassword && $newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_edit_coach_profile');
            }

            if ($newPassword) {
                $user->setPlainPassword($newPassword);
            }

            $uploadedFile = $form->get('profilePicture')->getData();
            
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $user->setProfilePicture($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil utilisateur a été mis à jour.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile/edit_user_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/coach/edit', name: 'app_edit_coach_profile')]
    #[IsGranted('ROLE_COACH')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager,FileUploader $fileUploader): Response
    {
        $coach = $this->getUser();
    
        if (!$coach) {
            return $this->redirectToRoute('app_login');
        }
    
        $form = $this->createForm(EditCoachProfileType::class, $coach);
        $form->handleRequest($request);
    

    
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $request->get('newPassword');
            $confirmPassword = $request->get('confirmPassword');
            if ($newPassword && $newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_edit_coach_profile');
            }
    
            if ($newPassword) {
                $coach->setPlainPassword($newPassword);
            }
    
            $uploadedFile = $form->get('profilePicture')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile, '/profile_pictures');
                $coach->setProfilePicture($newFilename);
            }
    
            $entityManager->persist($coach);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');
            return $this->redirectToRoute('app_coach_profile');
        }
    
        return $this->render('profile/edit_coach_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}