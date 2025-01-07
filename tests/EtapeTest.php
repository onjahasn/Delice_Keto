<?php

namespace App\Tests\Entity;

use App\Entity\Etape;
use App\Entity\Recette;
use PHPUnit\Framework\TestCase;

class EtapeTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $etape = new Etape();

        // Test du setter et getter pour "numEtape"
        $etape->setNumEtape('1');
        $this->assertEquals('1', $etape->getNumEtape(), 'Le numéro de l\'étape doit être correctement défini et récupéré.');

        // Test du setter et getter pour "description"
        $description = 'Couper les légumes.';
        $etape->setDescription($description);
        $this->assertEquals($description, $etape->getDescription(), 'La description doit être correctement définie et récupérée.');
    }
}
