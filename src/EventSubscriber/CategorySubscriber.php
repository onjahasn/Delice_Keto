<?php

namespace App\EventSubscriber;

use App\Repository\CategorieRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CategorySubscriber implements EventSubscriberInterface
{
    private $categoryRepository;
    private $twig;

    public function __construct(CategorieRepository $categoryRepository, Environment $twig)
    {
        $this->categoryRepository = $categoryRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Récupérer les catégories depuis la base de données
        $categories = $this->categoryRepository->findAll();

        // Ajouter les catégories globalement à Twig
        $this->twig->addGlobal('categories', $categories);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
