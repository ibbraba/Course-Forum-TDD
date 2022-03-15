<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
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


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AppController.php',
        ]);
    }
}
