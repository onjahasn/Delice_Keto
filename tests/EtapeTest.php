<?php

namespace App\Tests\Entity;

use App\Entity\Etape;
use PHPUnit\Framework\TestCase;

class EtapeTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $etape = new Etape();

        $etape->setNumEtape('1');
        $this->assertEquals('1', $etape->getNumEtape());

        $description = 'Couper les légumes.';
        $etape->setDescription($description);
        $this->assertEquals($description, $etape->getDescription());
    }
}
