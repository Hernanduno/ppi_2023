<?php
namespace App\Controller;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class PostController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/post/create', name: 'createPost')]
    public function createPost(Request $request): Response
    {
        $post = new Post();
        $post->setCreationDate(new \DateTime());
        $postForm = $this->createForm(PostType::class, $post);
        $createdMessage = "";

        $user =  $this->em->getRepository(User::class)->findOneBy(["id" => 1]);
        $post->setUserId($user);
        $post->setUrl("TEST URL");
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            $this->redirectToRoute('createPost');
            $createdMessage ="POST CREATED";
        }
        return $this->render('post/index.html.twig', [
            "postForm" => $postForm->createView(),
            "createdMessage"=> $createdMessage
        ]);
    }
    #[Route('/post/{id}', name: 'readPost')]
    public function readPost($id): Response
    {
        $postInfo =  $this->em->getRepository(Post::class)->findOneBy(["id" => $id]);
        return $this->render('post/index.html.twig', [
            'postInfo' => $postInfo
        ]);
    }
    #[Route('/post/update/{id}', name: 'updatePost')]
    public function updatePost($id,Request $request): Response
    {
        $post = $this->em->getRepository(Post::class)->findOneBy(["id" => $id]);
        $postForm = $this->createForm(PostType::class, $post);
        $user =  $this->em->getRepository(User::class)->findOneBy(["id" => 1]);
        $post->setUserId($user);
        $post->setUrl("TEST URL");
        $updatedMessage = "";
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $this->em->persist($post);
            $this->em->flush();
            $this->redirectToRoute('updatePost',["id"=>$id]);
            $updatedMessage = "POST UPDATED";
        }
        return $this->render('post/index.html.twig', [
            "postForm" => $postForm->createView(),
            "updatedMessage" => $updatedMessage
        ]);
    }
    #[Route('/post/delete/{id}', name: 'deletePost')]
    public function deletePost($id)
    {
        $post = $this->em->getRepository(Post::class)->findOneBy(["id" => $id]);
        $this->em->remove($post);
        $this->em->flush();
        return $this->render('post/index.html.twig', [
            "deletedMenssage" => "POST WITH ID ". $id . " WAS DELETED"
            
        ]);
    }
}
