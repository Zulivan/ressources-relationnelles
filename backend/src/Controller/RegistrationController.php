<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkMissing($data, $valeurs)
    {
        $maquants = [];
    
        foreach ($valeurs as $valeur) {
            if (!isset($data[$valeur]) || strlen($data[$valeur]) == 0) {
                
                $valeur = str_replace('_', ' ', $valeur);
                $valeur = ucfirst($valeur);
                
                $maquants[] = $valeur;
            }
        }

        return $maquants;
    }

    /**
     * @Route("/api/signup", name="registration")
     */
    public function register(Request $request)
    {
        // Get the data from the request body
        $data = json_decode($request->getContent(), true);
        if(!$data) {
            return $this->json([
                'message' => 'JSON invalide',
            ]);
        }
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['libelle' => 'Citoyen']);

        // Check if the data is valid
        $maquants = $this->checkMissing($data, ['nom', 'prenom', 'date_naissance', 'email', 'adresse', 'ville', 'code_postal', 'password']);
        if (count($maquants) > 0) {
            return $this->json([
                'message' => 'Les champs suivants sont manquants: '.implode(', ', $maquants),
            ]);
        }

        // Create a new user object and set its properties
        $user = new Utilisateur();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);

        // Check if the date is valid
        try {
            $user->setDateNaissance(new \DateTime($data['date_naissance']));
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Date de naissance invalide',
            ]);
        }
        $user->setEmail($data['email']);
        $user->setAdresse($data['adresse']);
        $user->setVille($data['ville']);
        $user->setCodePostal((int) $data['code_postal']);

        $user->setActif(true);
        $user->setRole($role);
        // Encode the password
        $password = (string) $data['password'];
        // Check if the password is valid
        if (strlen($password) < 8) {
            return $this->json([
                'message' => 'Le mot de passe doit contenir au moins 8 caractères',
            ]);
        }

        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

        // Save the user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Return a JSON response
        return $this->json([
            'message' => 'Votre compte a bien été créé!',
        ]);
    }
}
