<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategoryType;
use App\Repository\RecetteRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAllCategories();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/recettes/categorie/{id}', name: 'category_show', requirements: ['id' => '\\d+'])]
    public function recettesByCategorie(RecetteRepository $recetteRepository, int $id): Response
    {
        // Récupérer les recettes par catégorie
        $recettes = $recetteRepository->findByCategory($id);

        // Rendre la vue avec les recettes
        return $this->render('recette/categorie.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/categories/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categorie();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès !');
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Pour supprimer une categorie
    #[Route('/categories/{id}', name: 'category_delete', methods: ['POST'])]
    public function delete(Categorie $category, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'. $category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie supprimée avec succès!');
        }

        return $this->redirectToRoute('category_index');
    }
}
