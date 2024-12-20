<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'recette_index')]
    public function index(RecetteRepository $recetteRepository): Response
    {
        $recettes = $recetteRepository->findAll();

        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/{id}', name: 'recette_show', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function showRecetteWithComments(
        int $id,
        RecetteRepository $recetteRepository,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // Récupère la recette
        $recette = $recetteRepository->find($id);

        if (!$recette) {
            throw $this->createNotFoundException('Recette non trouvée.');
        }

        // Formulaire pour ajouter un commentaire
        if ($request->isMethod('POST')) {
            $description = $request->request->get('description');
            if ($description) {
                $commentaire = new Commentaire();
                $commentaire->setDescription($description);
                $commentaire->setCreatedAt(new \DateTime());
                $commentaire->setRecette($recette);
                $commentaire->setUser($this->getUser());

                $em->persist($commentaire);
                $em->flush();

                $this->addFlash('success', 'Commentaire ajouté avec succès.');
                return $this->redirectToRoute('recette_show', ['id' => $recette->getId()]);
            }
        }

        return $this->render('recette/detail.html.twig', [
            'recette' => $recette,
            'commentaires' => $recette->getCommentaires(), // Relation OneToMany
        ]);
    }


    #[Route('/recette/new', name: 'recette_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em, #[Autowire('%uploadDir%')] string $uploadDir): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();                
                $imageFile->move($uploadDir, $newFilename);  // Déplacer le fichier vers le répertoire d'upload                
                $recette->setImage($newFilename);  // Enregistrer le chemin ou le nom du fichier dans l'entité
            }
            // Associez les ingredients à la recette
            foreach ($recette->getIngredients() as $ingredient) {
                $ingredient->setRecette($recette);
            }
            // Associez l'étape à la recette
            foreach ($recette->getEtapes() as $index => $etape) {
                $etape->setRecette($recette); 
                $etape->setNumEtape($index + 1); // Définit un numéro d'étape unique
            }

            $recette = $form->getData();

            $em->persist($recette);
            $em->flush();

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/{id}/edit', name: 'recette_edit', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Recette $recette, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/{id}/delete', name: 'recette_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Recette $recette, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
            $em->remove($recette);
            $em->flush();
        }

        return $this->redirectToRoute('recette_index');
    }

    #[Route('/recettes/search', name: 'recette_search', methods: ['GET'])]
    public function search(Request $request, RecetteRepository $recetteRepository): Response
    {
        $query = $request->query->get('q', ''); // Récupère le terme de recherche

        // Recherche uniquement par catégorie et étape
        $recettes = $recetteRepository->searchByCategoryAndStep($query);

        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
            'query' => $query,
        ]);
    }


    // #[Route('/recettes/populaires', name: 'recette_populaires')]
    // public function populaires(): Response
    // {
    //     $recettesPopulaires = $this->em->getRepository(Recette::class)->findBy(
    //         [],
    //         ['views' => 'DESC'], // Trier par popularité, par exemple par le champ "views"
    //         5 // Limiter à 5 recettes populaires
    //     );

    //     return $this->render('recette/populaires.html.twig', [
    //         'recettesPopulaires' => $recettesPopulaires,
    //     ]);
    // }

    // #[Route('/recettes/nouvelles', name: 'recette_nouvelles')]
    // public function nouvelles(): Response
    // {
    //     $nouvellesRecettes = $this->em->getRepository(Recette::class)->findBy(
    //         [],
    //         ['createdAt' => 'DESC'], // Trier par date de création
    //         5 // Limiter à 5 recettes récentes
    //     );

    //     return $this->render('recette/nouvelles.html.twig', [
    //         'nouvellesRecettes' => $nouvellesRecettes,
    //     ]);
    // }
}
