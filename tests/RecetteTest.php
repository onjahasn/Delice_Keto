<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use App\Entity\Recette;
use PHPUnit\Framework\TestCase;

class RecetteTest extends TestCase
{
    public function testRecetteEntity()
    {
        $recette = new Recette();

        // Test du titre
        $titre = "Recette de test";
        $recette->setTitre($titre);
        $this->assertIsString($titre, $recette->getTitre());

        // Test de la date de création
        $date = new \DateTime('2025-01-06');
        $recette->setCreatedAt($date);
        $this->assertEquals($date, $recette->getCreatedAt());

        // Test du temps de préparation
        $tempsPreparation = 45;
        $recette->setTempsPreparation($tempsPreparation);
        $this->assertEquals($tempsPreparation, $recette->getTempsPreparation());

        // Test du nombre de personnes
        $nombrePersonne = 4;
        $recette->setNombrePersonne($nombrePersonne);
        $this->assertIsInt($nombrePersonne, $recette->getNombrePersonne());

        // Test de l'association avec une catégorie
        $categorie = new Categorie();
        $recette->setCategorie($categorie);
        $this->assertSame($categorie, $recette->getCategorie());
    }
}

