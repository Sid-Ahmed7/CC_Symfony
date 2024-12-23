<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/redirect', name: 'app_redirect')]
    public function redirectAfterLogin(): Response
    {
        $user = $this->getUser();
    
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_admin');
        }
    
        if (in_array('ROLE_COACH', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
    
        if (in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('app_home');
        }
    
        return $this->redirectToRoute('app_home');
    }
    
}
