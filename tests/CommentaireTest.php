<?php

namespace App\Tests\Entity;

use App\Entity\Commentaire;
use PHPUnit\Framework\TestCase;

class CommentaireTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $commentaire = new Commentaire();

        // Test du setter et getter pour "description"
        $commentaire->setDescription('Un commentaire test');
        $this->assertIsString('Un commentaire test', $commentaire->getDescription());

        // Test du setter et getter pour "createdAt"
        $date = new \DateTime('2023-01-01');
        $commentaire->setCreatedAt($date);
        $this->assertEquals($date, $commentaire->getCreatedAt());
    }
}
