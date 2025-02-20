<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionLegaleController extends AbstractController
{
    #[Route('/mention/legale', name: 'app_mention_legale')]
    public function index(): Response
    {
        return $this->render('mention_legale/index.html.twig', [
            'site_name' => 'Delice Keto',
            'site_url' => 'https://deliceketo.sytes.net',
            'editor_name' => 'Delice Keto',
            'editor_address' => 'Ton adresse',
            'editor_email' => 'contact@tonsite.com',
            'editor_phone' => '+33 6 12 34 56 78',
            'host_name' => 'Nom de l’hébergeur',
            'host_address' => 'Adresse complète de l’hébergeur',
            'host_phone' => 'Numéro de contact de l’hébergeur',
            'data_usage' => 'gestion des comptes et des commentaires',
        ]);
    }
}
