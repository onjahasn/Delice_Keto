<?php

namespace App\Tests\Entity;

use App\Entity\Ingredient;
use App\Entity\Recette;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{
    public function testIngredientEntity()
    {
        $ingredient = new Ingredient();

        // Test du nom
        $nom = "Farine d'amande";
        $ingredient->setNom($nom);
        $this->assertEquals($nom, $ingredient->getNom());

        // Test de la quantité
        $quantite = 200;
        $ingredient->setQuantite($quantite);
        $this->assertIsInt($quantite, $ingredient->getQuantite());

        // Test de l'association avec une recette
        $recette = new Recette();
        $ingredient->setRecette($recette);
        $this->assertSame($recette, $ingredient->getRecette());
    }
}
