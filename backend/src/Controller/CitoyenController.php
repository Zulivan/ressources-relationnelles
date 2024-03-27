<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Service\LoggedUser;

use App\Entity\Utilisateur;

class CitoyenController extends AbstractController
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

    #[Route('/api/citoyen', name: 'api_citoyen')]
    public function index(Request $req): JsonResponse
    {
        $data = $this->loggedUser->get();
        if(!$data){
            return new JsonResponse(['error' => "Non connecté"], 403);
        }

        $user = [
            'id' => $data->getId(),
            'moderator' => $this->loggedUser->isModerator(),
            'administrator' => $this->loggedUser->isAdministrator(),
            'super_administrator' => $this->loggedUser->isSuperAdministrator(),
            'nom' => $data->getNom(),
            'prenom' => $data->getPrenom(),
            'email' => $data->getEmail()
        ];
        return  new JsonResponse($user);
    }

}
