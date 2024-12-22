<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use App\Entity\User;
use App\Entity\Coach;

class Mailer
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $router;
    private Environment $twig;

    
    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * Envoie l'email de réinitialisation du mot de passe pour un utilisateur ou un coach.
     *
     * @param User|Coach $entity
     */
    public function sendResetPasswordEmail($entity): void
    {
        if (!$entity instanceof User && !$entity instanceof Coach) {
            throw new \InvalidArgumentException('L\'entité doit être de type User ou Coach.');
        }

        if($entity instanceof Coach) {
            $resetUrl = $this->router->generate('app_reset_password', ['token' => $entity->getResetToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        else {
            $resetUrl = $this->router->generate('app_reset_password', ['token' => $entity->getResetToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        $expirationDate = new \DateTime('+1 days');

       
        $email = (new Email())
            ->from('admin@gmail.com')
            ->to($entity->getEmail())
            ->subject('Réinitialisez votre mot de passe')
            ->html(
                $this->twig->render('email/reset.html.twig', [
                    'entity' => $entity,  
                    'resetUrl' => $resetUrl,
                    'expirationDate' => $expirationDate,
                ])
            );
        
        $this->mailer->send($email);
    }
}
