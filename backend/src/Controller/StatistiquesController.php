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
use App\Entity\Ressource;

class StatistiquesController extends AbstractController
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
    public function getBirthYearsStatistics()
    {
        $qb = $this->em->getRepository(Utilisateur::class)->createQueryBuilder('u');
        $qb->select('COUNT(u.id) as count, YEAR(u.dateNaissance) as annee');
    
        // Group by birth year
        $qb->addGroupBy('annee');
    
        // Order by birth year in ascending order
        $qb->orderBy('annee', 'ASC');
    
        $results = $qb->getQuery()->getResult();
    
        // Calculate age based on current year
        $currentYear = date('Y');
        foreach ($results as &$result) {
            $birthYear = $result['annee'];
            $age = $currentYear - $birthYear;
            $result['age'] = $age;
            unset($result['annee']); // Remove the 'annee' field
        }
    
        return $results;
    }

    public function getNombreUtilisateurs()
    {
        $qb = $this->em->getRepository(Utilisateur::class)->createQueryBuilder('u');
        $qb->select('COUNT(u.id) as count');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getNombreRessources()
    {
        $qb = $this->em->getRepository(Ressource::class)->createQueryBuilder('r');
        $qb->select('COUNT(r.id) as count');
        return $qb->getQuery()->getSingleScalarResult();
    }

    #[Route('/api/statistiques', name: 'api_statistiques')]
    public function stats(Request $req): JsonResponse
    {
        $nombreUtilisateurs = $this->getNombreUtilisateurs();
        $nombreRessources = $this->getNombreRessources();
        $statsAge = $this->getBirthYearsStatistics();

        $output = [
            'nombreUtilisateurs' => $nombreUtilisateurs,
            'nombreRessources' => $nombreRessources,
            'age' => $statsAge
        ];

        return  new JsonResponse($output);
    }

    #[Route('/api/statistiques/ages', name: 'api_statistiques_ages')]
    public function ages(Request $req): JsonResponse
    {
        $birthYearsData = $this->getBirthYearsStatistics();

        $nombreUtilisateurs = $this->getNombreUtilisateurs();

        
        $statistics = [];
        foreach ($birthYearsData as $row) {
            $year = $row['birthYear'];
            $count = $row['count'];
            $statistics[$year] = $count;
        }

        return  new JsonResponse($ages);
    }

}
