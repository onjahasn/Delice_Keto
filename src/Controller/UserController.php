<?php
// Déclaration du namespace. Cela permet de définir à quel "espace" ou "dossier" cette classe appartient
// Ici, la classe UserController appartient à l'espace "App\Controller"
namespace App\Controller;

use App\Entity\User; // On importe la classe User. Cela permet de manipuler les entités User
use App\Repository\UserRepository; // On importe le repository User pour accéder aux données de la table 'user' dans la base de données
use Doctrine\ORM\EntityManagerInterface; // On importe EntityManagerInterface qui est utilisé pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base de Symfony qui fournit des méthodes utiles pour les contrôleurs
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request qui gère les données envoyées dans la requête HTTP
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response qui est utilisée pour envoyer une réponse HTTP
use Symfony\Component\Routing\Annotation\Route; // On importe l'annotation Route, qui permet de définir des routes pour ce contrôleur

#[Route('/users')] // Cette annotation définit la route principale pour toutes les actions du contrôleur. Toutes les routes dans ce contrôleur commenceront par '/users'
class UserController extends AbstractController // Déclaration de la classe UserController qui étend la classe AbstractController (la classe de base des contrôleurs Symfony)
{
    #[Route('/', name: 'user_index', methods: ['GET'])] // Cette annotation définit la route pour afficher la liste des utilisateurs. 'GET' signifie que cette route répondra aux requêtes HTTP GET (les requêtes pour obtenir des données)
    public function index(UserRepository $userRepository): Response // La méthode index() récupère tous les utilisateurs de la base de données via UserRepository et les affiche
    {
        $users = $userRepository->findAll(); // Appelle la méthode findAll() du UserRepository pour récupérer tous les utilisateurs dans la base de données

        return $this->render('user/index.html.twig', ['users' => $users]); // Rendu de la vue 'user/index.html.twig', avec la liste des utilisateurs passée à la vue
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])] // La route '/new' pour afficher le formulaire de création et traiter l'envoi du formulaire
    public function new(Request $request, EntityManagerInterface $em): Response // La méthode new() gère l'affichage et la création de nouveaux utilisateurs
    {
        if ($request->isMethod('POST')) { // Si la méthode de la requête est POST (c'est-à-dire que le formulaire a été soumis)
            $user = new User(); // On crée une nouvelle instance de l'entité User

            $password = $request->request->get('password'); // Récupère le mot de passe depuis la requête
            $confirmPassword = $request->request->get('confirm_password');
            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,}$/'; // Définition du pattern pour le mot de passe
            // Vérifie si le mot de passe et la confirmation du mot de passe sont identiques
            if ($password !== $confirmPassword) { // Si les mots de passe ne correspondent pas
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.'); // Ajoute un message flash d'erreur
                return $this->redirectToRoute('user_new'); // Redirige vers le formulaire de création en cas d'erreur
            }

            if (!preg_match($pattern, $password)) { // Vérifie si le mot de passe respecte le pattern
                $this->addFlash('error', 'Le mot de passe doit contenir au moins 8 caractères et inclure des minuscules, majuscules, chiffres et caractères spéciaux.');
                return $this->redirectToRoute('user_new'); // Redirige vers le formulaire de création en cas d'erreur
            }

            // On récupère les données soumises dans le formulaire et on les attribue à l'entité $user
            $user->setEmail($request->request->get('email')); // Attribue l'email depuis la requête
            $user->setName($request->request->get('name')); // Attribue le nom de l'utilisateur depuis la requête
            $user->setUserName($request->request->get('username')); // Attribue le nom de l'utilisateur depuis la requête

            // Hachage du mot de passe avant de le sauvegarder dans la base de données
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Utilise bcrypt pour sécuriser le mot de passe
            $user->setPassword($hashedPassword); // On attribue le mot de passe haché à l'utilisateur

            $role = $request->request->get('role', 'ROLE_USER'); // On récupère le rôle du formulaire. Par défaut, il sera 'ROLE_USER'
            $user->setRoles([$role]); // Attribue le rôle à l'utilisateur

            $em->persist($user); // Prépare l'entité $user à être sauvegardée dans la base de données
            $em->flush(); // Sauvegarde réellement les données dans la base de données

            return $this->redirectToRoute('app_login'); // Redirige l'utilisateur vers la page de connexion
        }

        return $this->render('user/new.html.twig'); // Si la méthode est GET (formulaire de création), on affiche le formulaire
    }
    
    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])] // La route '/{id}/edit' permet de modifier un utilisateur existant
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response // La méthode edit() permet de modifier les informations d'un utilisateur existant
    {
        if ($request->isMethod('POST')) { // Si la requête est de type POST (formulaire soumis)
            // Récupère et met à jour les informations de l'utilisateur
            $user->setName($request->request->get('name')); // Modifie le nom de l'utilisateur
            $user->setUserName($request->request->get('username')); // Modifie le nom de l'utilisateur
            $user->setEmail($request->request->get('email')); // Modifie l'email de l'utilisateur

            $role = $request->request->get('role', 'ROLE_USER'); // Récupère et met à jour le rôle de l'utilisateur
            $user->setRoles([$role]); // Modifie le rôle de l'utilisateur

            $em->flush(); // Sauvegarde les modifications apportées à l'utilisateur dans la base de données

            return $this->redirectToRoute('user_show'); // Redirige vers la page de la liste des utilisateurs après modification
        }

        return $this->render('user/edit.html.twig', ['user' => $user]); // Affiche le formulaire avec les données de l'utilisateur à modifier
    }
    
    #[Route('/{id}/show', name: 'user_show', methods: ['GET'])] // La route '/{id}' permet d'afficher les détails d'un utilisateur
    public function show(User $user): Response // La méthode show() affiche les détails d'un utilisateur
    {
        return $this->render('user/show.html.twig', ['user' => $user]); // Rendu de la vue 'user/show.html.twig', avec les détails de l'utilisateur passés à la vue
    }


    #[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])] // La route '/{id}/delete' permet de supprimer un utilisateur
    public function delete(User $user, EntityManagerInterface $em): Response // La méthode delete() permet de supprimer un utilisateur existant
    {
        $em->remove($user);
        $em->flush(); // Sauvegarde la suppression dans la base de données

        return $this->redirectToRoute('user_index'); // Redirige vers la liste des utilisateurs après suppression
    }
}
