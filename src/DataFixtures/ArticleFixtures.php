<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        //créer 3 categories fakées
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);
            //Creer entre 04 et 06 articles
            for ($j = 1; $j <= mt_rand(4, 6); $j++) {

                $article = new Article();
                //paragraphs est un tableau il faut le mettre chaine de caractere, contenu avec plusieur paragraphes
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->DateTimeBetween('-6 month'))
                    ->setCategory($category);


                $manager->persist($article);
                for ($k = 1; $k <= \mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    //paragraphs est un tableau il faut le mettre chaine de caractere, contenu avec plusieur paragraphes
                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                    //recuperation de la date de creation de l'article ensuite la diffence entre cette date et maintenant
                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days= $interval->days;
                    //passer a faker 100 days
                    $minimum='-'.$days.' days';//-100 days
                    $comment->setAuthor($faker->name())
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);

                        $manager->persist($comment);
                }
            }
        }


        $manager->flush();
    }
}
