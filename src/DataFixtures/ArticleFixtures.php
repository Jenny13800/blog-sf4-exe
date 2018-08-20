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

        // 3 catégories fake
        for($i = 1; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            // créer entre 4 et 6 articles
            // mt_rand = nb au hasard
            for($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();

                // setContent attend chaîne de caractère
                // $faker->paragraphs() => retourne un tableau
                // rejoindre la fin du tableau avec une balise p fermante et ouverte
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                // on donne des commentaires à l'articles
                for($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();

                    // date d'aujourd'hui
                    //$now = new \DateTime();
                    // interval entre aujourd'hui et la date de création de l'article
                    /*$interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . ' days'; // par ex : -100 days*/

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
