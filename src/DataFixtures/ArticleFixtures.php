<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Initialiser Faker
        $faker = Factory::create();

        // Liste de catégories réalistes
        $categoriesNoms = [
            'Vêtements hommes',
            'Vêtements femmes',
            'Chaussures',
            'Accessoires',
            'Sportswear',
            'Tenues de soirée',
            'Tenues décontractées',
            'Sous-vêtements',
            'Sacs',
            'Bijoux',
        ];

        $categories = [];

        // Créer les catégories
        foreach ($categoriesNoms as $nomCategorie) {
            $categorie = new Categorie();
            $categorie->setNom($nomCategorie);
            $manager->persist($categorie);
            $categories[] = $categorie; // Stocker pour assigner aux articles
        }

        // Liste de types d'articles pour les titres
        $types = [
            'T-shirt',
            'Jean',
            'Veste',
            'Robe',
            'Jupe',
            'Chemise',
            'Short',
            'Pantalon',
            'Baskets',
            'Sandales',
            'Écharpe',
            'Ceinture',
            'Montre',
            'Collier',
            'Bracelet',
        ];

        // Générer 30 articles factices
        for ($i = 0; $i < 30; $i++) {
            $article = new Article();

            // Générer un titre réaliste
            $article->setTitre($faker->randomElement($types) . ' ' . $faker->colorName . ' ' . $faker->word);

            // Générer un contenu descriptif réaliste
            $article->setContenu(
                $faker->sentence(5) . ' ' .
                    'Disponible en tailles ' . $faker->randomElement(['S', 'M', 'L', 'XL']) . '. ' .
                    'Couleur : ' . $faker->colorName . '. ' .
                    'Prix : ' . $faker->randomFloat(2, 10, 150) . ' €.'
            );

            // Associer l'article à une catégorie aléatoire
            $article->setCategory($faker->randomElement($categories));

            $manager->persist($article);
        }

        // Enregistrer en base de données
        $manager->flush();
    }
}
