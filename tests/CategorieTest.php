<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use App\Entity\Recette;
use PHPUnit\Framework\TestCase;

class CategorieTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $categorie = new Categorie();
        $categorie->setNom('Dessert');

        $this->assertIsString('Dessert', $categorie->getNom());
    }

    public function testAddRecette(): void
    {
        $categorie = new Categorie();
        $recette = new Recette();

        $categorie->addRecette($recette);

        $this->assertTrue($categorie->getRecettes()->contains($recette));
        $this->assertEquals($categorie, $recette->getCategorie());
    }
}
