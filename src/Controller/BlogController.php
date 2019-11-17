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

     //défois appeler pour afficher les données du formulaire, défois pour créer(enregistrer les données de formulaire)
    public function create(Request $request, ObjectManager $manager)
    {
        //annalyse des données de la requete
        dump($request);
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
       

        }
        return $this->render("blog/create.html.twig");
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
