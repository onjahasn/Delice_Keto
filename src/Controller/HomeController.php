<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) // Injection de dépendance
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(RecetteRepository $recetteRepository): Response
    {
        $carousel = $this->em->getRepository(Recette::class)->findAll();
        $totalRecettes = $this->em->getRepository(Recette::class)->count([]);
        $recettes = $recetteRepository->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('home/index.html.twig', [
            'carousel' => $carousel,
            'totalRecettes' => $totalRecettes,
            'recettes' => $recettes,
        ]);
    }
}

