<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commentaires')]
class CommentaireController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CommentaireRepository $commentaireRepository, Request $request): Response
    {
        $commentaires = $commentaireRepository->findAll();

        // Vérifie le paramètre "format" dans la requête
        if ($request->query->get('format') === 'json') {
            return $this->json($commentaires, 200, [], ['groups' => 'comment:read']);
        }

        return $this->render('commentaire/list.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(Commentaire $commentaire, Request $request): Response
    {
        if ($request->query->get('format') === 'json') {
            return $this->json($commentaire, Response::HTTP_OK, [], ['groups' => 'comment:read']);
        }

        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE', 'POST'])]
    public function delete(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
    {
        // Vérifie si c'est une requête DELETE pour l'API
        if ($request->isMethod('DELETE')) {
            $em->remove($commentaire);
            $em->flush();

            return $this->json(['message' => 'Commentaire supprimé'], Response::HTTP_NO_CONTENT);
        }
        $em->remove($commentaire);
        $em->flush();

        return $this->redirectToRoute('list');
    }

    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        RecetteRepository $recetteRepository
    ): Response {
        // Gestion des requêtes POST (création)
        if ($request->isMethod('POST')) {
            $description = $request->request->get('description');
            $recetteId = $request->request->get('recette_id'); // depuis un formulaire HTML

            // Validation : Vérifier si recette_id est valide
            if (!$recetteId) {
                $this->addFlash('error', 'L\'ID de la recette est manquant.');
                return $this->redirectToRoute('create');
            }

            $recette = $recetteRepository->find($recetteId);
            if (!$recette) {
                $this->addFlash('error', 'La recette spécifiée est introuvable.');
                return $this->redirectToRoute('create');
            }

            if ($description) {
                $commentaire = new Commentaire();
                $commentaire->setDescription($description);
                $commentaire->setCreatedAt(new \DateTime());
                $commentaire->setRecette($recette);
                $commentaire->setUser($this->getUser());

                $em->persist($commentaire);
                $em->flush();

                $this->addFlash('success', 'Commentaire créé avec succès.');

                return $this->redirectToRoute('list');
            }
        }

        // Formulaire pour Twig
        return $this->render('commentaire/create.html.twig');
    }
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Commentaire $commentaire,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($request->query->get('format') === 'json' && $request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            if (!$data['description']) {
                return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
            }

            $commentaire->setDescription($data['description']);
            $em->flush();

            return $this->json($commentaire, Response::HTTP_OK, [], ['groups' => 'comment:read']);
        }

        // Twig: Edition via formulaire HTML
        if ($request->isMethod('POST')) {
            $description = $request->request->get('description');
            if ($description) {
                $commentaire->setDescription($description);
                $em->flush();

                return $this->redirectToRoute('list');
            }
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

}

