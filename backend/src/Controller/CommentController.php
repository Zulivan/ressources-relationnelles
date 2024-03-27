<?php
namespace App\Controller;

// use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Comment;
use App\Service\LoggedUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    private $loggedUser;
    // private $validator;

    public function __construct(LoggedUser $loggedUser)
    {
        $this->loggedUser = $loggedUser;
        // $this->validator = $validator;
    }

    #[Route(path: '/comments', name: 'add_comment', methods: ['POST'])]
    public function addComment(Request $request): Response
    {
        $data = $request->attributes->get('data');

        // Get the logged in user
        $loggedInUser = $this->loggedUser->get();

        // Create a new Comment entity and set its properties
        $comment = new Comment();
        $comment->setUser($loggedInUser);
        $comment->setContent($data['content']);
        // ...

        // Validate the entity
        // $this->validator->validate($comment);

        // Save the comment to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        // Return a response indicating success
        return new Response('Comment added successfully', Response::HTTP_CREATED);
    }
}
?>