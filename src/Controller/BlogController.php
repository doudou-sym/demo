<?php

namespace App\Controller;

use App\Entity\Article;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use PhpParser\Node\Stmt\Return_;
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
     */
    public function create(Request $request, ObjectManager $manager)
    {
        /*dump($request);
        if($request->request->count()>0){
            $article=new Article();
            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreatedAt(new \DateTime());

        $manager->persist($article);
        $manager->flush();
        //page de redirection apres la creation de l'article
        return $this->redirectToRoute('blog_show', [
            'id'=>$article->getId()
        ]);
       

        }*/

        //article vide
        $article=new Article();


        //createformbulder() pour crer un formulaire a une entite(article)
        $form=$this->createFormBuilder($article)  //ensuite il faut le configurer avec les champs (ajouter des champs a ce formulaire)
                  /*->add('title', TextType::class,[
                       'attr' => [
                           'placeholder' => "Titre de l'article",
                           //'class' => 'form-control'
                       ]
                   ])
                   //->add('title', TextType::class)
                   ->add('content', TextareaType::class)
                   ->add ('image') 
                   /*->add('save', SubmitType::class, [
                       'label' => 'enregister'
                   ])*/
                   /*//ensuite pour voir le resultat finale avec getForm()
                   ->add('title', TextType::class,[
                    'attr' => [
                        'placeholder' => "Titre de l'article",
                        //'class' => 'form-control'
                    ]
                ])*/
                ->add('title')
                ->add('content')
                ->add ('image') 
                /*->add('save', SubmitType::class, [
                    'label' => 'enregister'
                ])*/
                //ensuite pour voir le resultat finale avec getForm()
                   ->getForm(); 

                   //Analaliser la requet HTTP passée en parametre, si soumis ou pas
                   $form->handleRequest($request);
                  dump($article);
                  if($form->isSubmitted() && $form->isValid()){
                      $article->setCreatedAt(new \DateTime());
                      $manager->persist($article);
                      $manager->flush();
                      return $this->redirectToRoute(('blog_show'), ['id'=>$article->getId()]);
                  }
                

//ensuite afficher le formulaire, le passer a twig, o, passe pas $form on passe une variable facile a afficher (cretaeView()--> creer un petit objet resultat de ce formulaire)


        return $this->render("blog/create.html.twig", [
            'formArticle'=> $form->createView()
        ]);
    }

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
