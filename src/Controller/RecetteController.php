<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use App\Service\MongoDBService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository, MongoDBService $mongoDbService, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1); // Récupère le numéro de page depuis la requête
        $limit = 8; // Nombre de recettes par page

        $queryBuilder = $recetteRepository->createQueryBuilder('r')  // Crée un QueryBuilder pour récupérer les recettes validées            
            ->where('r.isValidatedAt = :validated') // Ajoute une condition pour ne récupérer que les recettes validées
            ->setParameter('validated', true)  // Définit la valeur du paramètre "validated" à true           
            ->orderBy('r.createdAt', 'DESC');  // Trie les recettes par date de création dans l'ordre décroissant

        $pagination = $paginator->paginate(  // Utilise le paginator pour paginer les résultats de la requête
            $queryBuilder->getQuery(),  // Exécute la requête générée par le QueryBuilder           
            $page,                       // Définit le numéro de la page actuelle           
            $limit                       // Définit le nombre de recettes par page
        );

        #$mongoDbService->insertVisit('recette');

        return $this->render('recette/index.html.twig', [
            'pagination' => $pagination,
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
            $recette->setUser($this->getUser());  // Associer la recette à l'utilisateur connecté
            $recette->setValidatedAt(false); // La recette est non validée par défaut

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
    // #[IsGranted('ROLE_ADMIN')]
    public function edit(Recette $recette, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est le propriétaire de la recette
        if ($recette->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette recette.');
        }
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recette);
            $entityManager->flush();
            $this->addFlash('success', 'Recette mise à jour avec succès.');

            return $this->redirectToRoute('recette_index');
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recette/{id}/delete', name: 'recette_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    // #[IsGranted('ROLE_ADMIN')]
    public function delete(Recette $recette, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
            $em->remove($recette);
            $em->flush();
        }

        return $this->redirectToRoute('recette_index');
    }

    #[Route('/recettes/search', name: 'recette_search', methods: ['GET'])]
    public function search(Request $request, RecetteRepository $recetteRepository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q', '');
        $page = $request->query->getInt('page', 1);
        $limit = 8;

        $results = $recetteRepository->searchByCategoryAndStep($query);

        $pagination = $paginator->paginate(
            $results,
            $page,
            $limit
        );

        return $this->render('recette/index.html.twig', [
            'pagination' => $pagination,
            'query' => $query,
        ]);
    }

    #[Route('/mes-recettes', name: 'user_recipes', methods: ['GET'])]
    public function mesRecettes(RecetteRepository $recetteRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos recettes.');
        }

        // Récupérer les recettes de l'utilisateur
        $mesRecettes = $recetteRepository->findBy(['user' => $user]);

        return $this->render('recette/mes_recettes.html.twig', [
            'mesRecettes' => $mesRecettes,
        ]);
    }

    #[Route('/dernieres-recettes', name: 'dernieres_recettes')]
    public function dernieresRecettes(RecetteRepository $recetteRepository): Response
    {
        // Récupérer les 5 dernières recettes
        $dernieresRecettes = $recetteRepository->findBy(
            ['isValidatedAt' => true],
            ['createdAt' => 'DESC'],
            4
        );

        // Retourner la vue Twig
        return $this->render('recette/dernieres_recettes.html.twig', [
            'dernieresRecettes' => $dernieresRecettes,
        ]);
    }

    #[Route('/recette/{id}/comments-count', name: 'comments_count', methods: ['GET'])]
    public function getCommentsCount(Recette $recette): JsonResponse
    {
        return new JsonResponse(['comments_count' => $recette->getCommentaires()->count()]);
    }
}
