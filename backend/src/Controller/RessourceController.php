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

// serializer interface
use Symfony\Component\Serializer\SerializerInterface;

use App\Entity\Utilisateur;
use App\Entity\Ressource;
use App\Entity\Categorie;
use App\Entity\TypeRessource;
use App\Entity\Commentaire;

class RessourceController extends AbstractController
{
    private $em;
    private $loggedUser;
 
    public function __construct(EntityManagerInterface $entityManager, LoggedUser $loggedUser, SerializerInterface $serializer)
    {
        $this->em = $entityManager;
        $this->loggedUser = $loggedUser;
        $this->serializer = $serializer;
    }

    #[Route('/api/ressource_comment', name: 'api_ressource_comment_add')]
    public function ajoutCommentaire(Request $request): Response
    {
        $user = $this->loggedUser->get();
        if(!$user){
            return new JsonResponse(['error' => $user], 404);
        }
        $commentaire = new Commentaire();

        if(!$request->getContent()){
            return new JsonResponse(['error' => 'Aucun contenu'], 404);
        }

        try{
            $data = json_decode($request->getContent(), true);

            if(!isset($data['message']) || !isset($data['ressource'])){
                return new JsonResponse(['error' => 'Paramètres manquants'], 404);
            }

            if(empty($data['message'])){
                return new JsonResponse(['error' => 'Message vide'], 404);
            }

            $commentaire->setUtilisateur($user);
            $commentaire->setText($data['message']);
            $commentaire->setRessource($this->em->getRepository(Ressource::class)->find($data['ressource']));

            if(isset($data['reponse'])){
                $commentaire->setReponse($this->em->getRepository(Commentaire::class)->find($data['reponse']));
            }

            $this->em->persist($commentaire);
            $this->em->flush();
        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }

        return new JsonResponse(['message' => 'Commentaire ajouté avec succès', 'id' => $commentaire->getId()], 201);
    }

    #[Route('/api/ressource_comment/{id}', name: 'api_ressource_comment_delete', methods: ['DELETE'])]
    public function deleteCommentaire(Request $request, int $id): Response
    {
        $user = $this->loggedUser->get();
        if(!$user){
            return new JsonResponse(['error' => $user], 404);
        }
        $commentaire = $this->em->getRepository(Commentaire::class)->find($id);
        if(!$commentaire){
            return new JsonResponse(['error' => 'Commentaire non trouvé'], 404);
        }
        if($commentaire->getUtilisateur()->getId() != $user->getId() && !$this->loggedUser->isModerator()){
            return new JsonResponse(['error' => 'Vous n\'êtes pas l\'auteur de ce commentaire'], 403);
        }
        
        try{
            $this->em->remove($commentaire);
            $this->em->flush();
        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }

        return new JsonResponse(['message' => 'Commentaire supprimé avec succès'], 201);
    }

    #[Route('/api/ressources/{id}/favori', name: 'api_ressource_favori', methods: ['POST'])]
    public function favoriRessource(Request $request, int $id): Response
    {
        $user = $this->loggedUser->get();
        if(!$user){
            return new JsonResponse(['error' => $user], 404);
        }
        $ressource = $this->em->getRepository(Ressource::class)->find($id);
        if(!$ressource){
            return new JsonResponse(['error' => 'Ressource non trouvée'], 404);
        }
        
        // Si la ressource est déjà dans les favoris, on la supprime
        if($user->getFavoris()->contains($ressource)){
            $user->removeFavori($ressource);
        }else{
            $user->addFavori($ressource);
        }

        $this->em->persist($user);
        $this->em->flush();


        return new JsonResponse(['message' => 'Favori mis à jour avec succès'], 201);
    }


    #[Route('/api/ressources/{id}/valider', name: 'api_ressource_valider', methods: ['PUT'])]
    public function validerRessource(Request $request, int $id): Response
    {
        $user = $this->loggedUser->get();
        $ismoderator = $this->loggedUser->isModerator();
        if(!$user || !$ismoderator){
            return new JsonResponse(['error' => 'inabilité'], 403);
        }
        $ressource = $this->em->getRepository(Ressource::class)->find($id);
        if(!$ressource){
            return new JsonResponse(['error' => 'Ressource non trouvée'], 404);
        }
        $ressource->setValidee(true);
        $ressource->setValidateur($user);
        $this->em->persist($ressource);
        $this->em->flush();

        return new JsonResponse(['message' => 'Ressource validée avec succès'], 201);
    }

    #[Route('/api/ajout_ressource', name: 'api_ressource_add')]
    public function ajout(Request $request): Response
    {
        $user = $this->loggedUser->get();
        if(!$user){
            return new JsonResponse(['error' => $user], 404);
        }
        // Utilisateur connecté, création ressource
        $ressource = new Ressource();
        
        $data = $_POST;

        $ressource->setLien($data['lien']);
        $ressource->setTitre($data['titre']);
        $ressource->setTexte($data['contenu']);
        $ressource->setDateCreation(new \DateTime());
        $ressource->setCreateur($user);
        $ressource->setValidateur($user);
        $ressource->addCategory($this->em->getRepository(Categorie::class)->find($data['categorie']));
        $ressource->setTypeRessource($this->em->getRepository(TypeRessource::class)->find($data['type']));
        
        // Gestion de l'image et modification type de ressource
        $files = $request->files->all();
        if($files){
            $ressource->setTypeRessource($this->em->getRepository(TypeRessource::class)->find(1));
            
            $fich = $request->files->get('fichier');
            $fileName = md5(uniqid()).'.'.$fich->guessExtension();
            
            $fich->move(
                $this->getParameter('uploads_directory'),
                $fileName
            );

            $filepath = $this->getParameter('uploads_directory').'/'.$fileName;
            $filepath = str_replace('../public', '', $filepath);
            

            $ressource->setLien($filepath);
        }

        try{
            $this->em->persist($ressource);
            $this->em->flush();
            return new JsonResponse(['error' => 'Ressource ajoutée avec succès', 'message' => 'Ressource ajoutée avec succès', 'id' => $ressource->getId()], 201);
        }catch(\Exception $e){
            return new JsonResponse(['error' => $e->getMessage()], 503);
        }
    }
}
