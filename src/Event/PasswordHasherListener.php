<?php

namespace App\Event;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Coach;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Coach::class)]
#[AsEntityListener(event: Events::preUpdate, method:'preUpdate', entity:User::class)]
#[AsEntityListener(event: Events::preUpdate, method:'preUpdate', entity:Coach::class)]
class PasswordHasherListener 
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(User|Coach $entity, PrePersistEventArgs $event): void
    {
        $this->hashPassword($entity);
    }

    public function preUpdate(User|Coach $entity, PreUpdateEventArgs $event): void
    {
        $this->hashPassword($entity);
    }

    private function hashPassword(User|Coach $entity): void
    {
        
        if ($entity->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword());
            $entity->setPassword($hashedPassword);
            $entity->eraseCredentials(); 
        }
    }
}
