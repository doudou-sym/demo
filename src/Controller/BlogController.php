<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use PhpParser\Node\Stmt\Return_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }
    /**
     * @route ("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue les amis!",
            'age' => 31
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    //modification de la methode create par form pour permettre a la fois la creation et la modification d'un article
    //au lieu de passer l'identifiant de l'article pour l'editer on passe l'article
    //par fois l'article a null pour que le new marchen
    public function form(Article $article=null, Request $request, ObjectManager $manager)
    {

        //article null
        if(!$article){
        $article = new Article();
        }

        //createformbulder() pour crer un formulaire a une entite(article)
        $form = $this->createFormBuilder($article)  //ensuite il faut le configurer avec les champs (ajouter des champs a ce formulaire)
                     ->add('title')
                     ->add('category', EntityType::class, [
                         'class'=> Category::class,
                         'choice_label'=>'title'
                     ])
                     ->add('content')
                     ->add('image')
                    ->getForm();
        //creation du formulaire a partir de la classe ArticleType(formulaire automatique cree dans le terminal)
        //$form=$this->createForm(ArticleType::class,$article)
        //Analaliser la requet HTTP passée en parametre, si soumis ou pas
        $form->handleRequest($request);
        dump($article);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
            $article->setCreatedAt(new \DateTime());}
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute(('blog_show'), ['id' => $article->getId()]);
        }


        //ensuite afficher le formulaire, le passer a twig, on passe pas $form on passe une variable facile a afficher (cretaeView()--> creer un petit objet resultat de ce formulaire)
        return $this->render("blog/create.html.twig", [
            'formArticle' => $form->createView(),
            //passer un boolen pour le choix du button, true si l'article existe
            'editMode'=>$article->getId() !==null
        ]);
    }
    /**
     * @Route
     */

    /**
     * @Route ("/blog/{id}", name="blog_show")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article
            ]

        );
    }
}
