<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostType;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AppController extends AbstractController
{
    /**
     * @Route("/app", name="app_app")
     * @param Request $request
     * @param ManagerRegistry $managerRegistry
     * @return Response
     */
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function userPosts($id, PostRepository $postRepository)
    {

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
     *
     */
    public function singlePost($id, PostRepository $postRepository, Request $request, UserRepository $userRepository, LikeRepository $likeRepository, ManagerRegistry $managerRegistry): Response
    {

        $post = $postRepository->find($id);

        $replies = $post->getComments();
        $user = $userRepository->find(1);

        $countLikes = $likeRepository->countLikesOnPost($id);


        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
            'replies' => $replies,
            'likesCount' => $countLikes
        ]);

    }

    /**
     * @Route ("create-post", name="new-post")
     * @IsGranted ("ROLE_ADMIN")
     */
    public function createPost(Request $request, ManagerRegistry $managerRegistry, UserRepository $userRepository): Response
    {
        $post = new Post();

        $user = $this->getUser();


        $form = $this->createForm(PostType::class, $post);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuteur($user);

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

    /**
     * @param Post $post
     * @param ObjectManager $objectManager
     * @param LikeRepository $likeRepository
     * @return Response
     * @Route ("post/{id}/like", name="likePost")
     */
    public function like($id, PostRepository $postRepository, ManagerRegistry $managerRegistry, LikeRepository $likeRepository): Response
    {
        $em = $managerRegistry->getManager();

        $post = $postRepository->find($id);

        $user = $this->getUser();

        // EARLY RETURN UNAUTHORIZED
        if (!$user) {
            return $this->json(["code" => 403, "message" => "Unauthorized"], 403);
        } else {

            $isLiked = $likeRepository->checkLike($post, $user);

            if ($isLiked) {
                //TODO: Remove like
                //FIND THE LIKE
                $like = $likeRepository->findOneBy([
                    "post" => $post,
                    "user" => $user,
                ]);
                $em->remove($like);
                $em->flush();
                return $this->json(["code" => 200, "message" => "Post deliké !!"], 200);


            } else {
                //TODO: Add like
                $newLike = new Like();
                $newLike->setPost($post)
                        ->setUser($user);
                $em->persist($newLike);
                $em->flush();

                return $this->json(["code" => 200, "message" => "Liké !!"], 200);
            }
        }
        /*        return $this->json(["code" => 200 , "message" => "C'est OK"], 200);*/


    }

}
