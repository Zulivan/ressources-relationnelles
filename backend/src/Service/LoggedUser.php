<?php

namespace App\Service;

//EntityManagerInterface
use Doctrine\ORM\EntityManagerInterface;
//TokenStorageInterface
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
// entity utilisateurs
use App\Entity\Utilisateur;

class LoggedUser
{
    private $em;
    private $tokenStorage;
 
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function get(): ?Utilisateur
    {
        try {
            $token = $this->tokenStorage->getToken();
            if(!$token){
                return null;
            }
            $user = $this->tokenStorage->getToken()->getUser();
            if(!$user){
                return null;
            }
            $user_id = $this->tokenStorage->getToken()->getUser()->getUserIdentifier();
            $data = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $user_id]);
            return $data;
        } catch (\Exception $e) {
            // return null;
            dd($e->getMessage());
        }
    }

    public function isModerator(): bool
    {
        $user = $this->get();
        if(!$user){
            return false;
        }
        return $this->get()->isModerator();
    }

    public function isAdministrator(): bool
    {
        $user = $this->get();
        if(!$user){
            return false;
        }
        return $this->get()->isAdministrator();
    }

    public function isSuperAdministrator(): bool
    {
        $user = $this->get();
        if(!$user){
            return false;
        }
        return $this->get()->isSuperAdministrator();
    }
}