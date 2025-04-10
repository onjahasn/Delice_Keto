<?php

namespace App\Controller;

use App\Entity\Recette;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/recette/{id}/validee', name: 'recette_validee', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function validate(Recette $recette, EntityManagerInterface $em): Response
    {
        $recette->setValidatedAt(true); // Valider la recette
        $em->flush();

        $this->addFlash('success', 'La recette a été validée avec succès.');
        return $this->redirectToRoute('recette_index');
    }

    #[Route('/admin/recettes/non-validees', name: 'recette_non_validees')]
    #[IsGranted('ROLE_ADMIN')]
    public function nonValidees(RecetteRepository $recetteRepository): Response
    {
        $recettes = $recetteRepository->findBy(['isValidatedAt' => false]); // Récupérer les recettes non validées

        return $this->render('admin/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }
}

// #[Route('/api/admin', name: 'api_admin_index', methods: ['GET'])]
    // public function index(): JsonResponse
    // {
    //     return new JsonResponse(['message' => 'Bienvenue']);
    // }

    // #[Route('/api/users', name: 'api_users_index', methods: ['GET'])]
    // public function getUsers(UserRepository $userRepository): JsonResponse
    // {
    //     $users = $userRepository->findAll();

    //     $response = array_map(function (User $user) {
    //         return [
    //             'id' => $user->getId(),
    //             'name' => $user->getName(),
    //             'username' => $user->getUserName(),
    //             'email' => $user->getEmail(),
    //             'roles' => $user->getRoles(),
    //         ];
    //     }, $users);

    //     return new JsonResponse($response);
    // }

    // #[Route('/api/users/new', name: 'api_user_new', methods: ['POST'])]
    // public function createUser(Request $request, EntityManagerInterface $em): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $user = new User();
    //     $user->setName($data['name']);
    //     $user->setUserName($data['username']);
    //     $user->setEmail($data['email']);

    //     $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    //     $user->setPassword($hashedPassword);

    //     $role = $data['role'] ?? 'ROLE_USER';
    //     $user->setRoles([$role]);

    //     $em->persist($user);
    //     $em->flush();

    //     return new JsonResponse(['message' => 'Utilisateur crée avec succés'], 201);
    // }

    // #[Route('/api/users/{id}/edit', name: 'api_user_edit', methods: ['PUT'])]
    // public function editUser(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     $user->setName($data['name']);
    //     $user->setUserName($data['username']);
    //     $user->setEmail($data['email']);

    //     $role = $data['role'] ?? 'ROLE_USER';
    //     $user->setRoles([$role]);

    //     $em->flush();

    //     return new JsonResponse(['message' => 'Utilisateur mis à jour avec succés']);
    // }

    // #[Route('/api/users/{id}/delete', name: 'api_user_delete', methods: ['DELETE'])]
    // public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    // {
    //     $em->remove($user);
    //     $em->flush();

    //     return new JsonResponse(['message' => 'Utilisateur supprimer avec succés']);
    // }
