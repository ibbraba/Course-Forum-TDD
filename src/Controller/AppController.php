<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/app", name="app_app")
     * @param Request $request
     * @param ManagerRegistry $managerRegistry
     * @return Response
     * TODO Get Post and User connected
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $post= new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

        }

        return $this->render("app/index.html.twig", [
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route ("posts/user/{id}", name="user-posts")
     * @param $id
     */
    public function userPosts($id, PostRepository $postRepository){

        $posts = $postRepository->findBy([
            "auteur" => $id
        ]);


        return $this->render("app/userposts.html.twig", [
            'posts' => $posts
        ]);
    }


    /**
     * @Route ("post/{id}", name="single-post")
     * @param $id
     */
    public function singlePost($id, PostRepository $postRepository, Request $request, UserRepository $userRepository, ManagerRegistry $managerRegistry): Response{

        $post = $postRepository->find($id);

        $replies = $post->getComments();
        $user = $userRepository->find(1);


        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($user);
            $comment->setPost($post);
           $entityManager = $managerRegistry->getManager();
           $entityManager->persist($comment);
           $entityManager->flush();
            //FLASH
            $this->addFlash("success", "Commentaire ajouté !");

            return $this->redirectToRoute("single-post", [
               "id" => $id
           ], Response::HTTP_SEE_OTHER);



        }


        return $this->render("app/single-post.html.twig", [
            'post' => $post,
            'form' => $form->createView(),
            'replies' => $replies
        ]);

    }

    /**
     * @Route ("create-post", name="new-post")
     */
    public function createPost(Request $request, ManagerRegistry $managerRegistry, UserRepository $userRepository): Response{
        $post = new Post();

        if ($this->getUser()) {
            $user = $this->getUser();
        }else{
            $user= $userRepository->find(1);
        }

        $form = $this->createForm(PostType::class, $post);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash("success", "Post Envoyé!");
            return $this->redirectToRoute("single-post", [
                "id" => $post->getId()
            ]);

        }

        return $this->render("app/new.html.twig", [
            'form' => $form->createView()
        ]);

    }



}
