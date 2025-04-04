<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiCommentaireTest extends ApiTestCase
{
    public function testGetCommentaires(): void
    {
        $client = static::createClient();

        // Vérifie que l'endpoint retourne une réponse 200
        $client->request('GET', '/api/commentaires?format=json');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // Vérifie que le contenu JSON est conforme
        $this->assertJsonContains([
            [
                'id' => 3,
                'description' => 'J\'aime beaucoup le gratin de noix!',
            ]
        ]);
    }
}

