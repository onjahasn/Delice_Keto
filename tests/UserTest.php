<?php

namespace App\Tests\Entity;

use App\Entity\Commentaire;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity()
    {
        $user = new User();

        // Test de l'email
        $email = "test@gmail.com";
        $user->setEmail($email);
        $this->assertNotNull($email, $user->getEmail());

        // Test du mot de passe
        $password = "securepassword123";
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());

        // Test du nom
        $name = "Hasina";
        $user->setName($name);
        $this->assertEquals($name, $user->getName());
    }
}
