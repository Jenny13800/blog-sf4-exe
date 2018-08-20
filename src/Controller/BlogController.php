<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        //plus besoin grace à l'injection de dépendance
        //$repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(){
        return $this->render('blog/home.html.twig');
    }

    /*public function show(ArticleRepository $repo,$id){
        //$repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }*/

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager){

        // pas d'articles
        if(!$article){
            $article = new Article();
        }

        /*$form = $this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();*/

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        //dump($article);
        if($form->isSubmitted() && $form->isValid()){
            // si l'article n'a pas d'id => alors il n'existe pas
            // donc date de création
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig',[
            'formArticle' => $form->createView(),
            // si est différent de null / alors true => donc edit
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Article $article){

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }


}
