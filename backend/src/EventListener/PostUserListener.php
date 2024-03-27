<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Service\LoggedUser;

class PostUserListener
{
    private $em;
    private $tokenStorage;
    private $loggedUser;
 
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, LoggedUser $loggedUser)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->loggedUser = $loggedUser;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Only set the user if the entity has a setUser method
        if (method_exists($entity, 'setUtilisateur')) {
            $user = $this->loggedUser->get();

            if ($user) {
                $entity->setUtilisateur($user);
            }
        }
    }
}
?>