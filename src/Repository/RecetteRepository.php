<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recette>
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    public function findByCategory($categoryId): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.categorie', 'c')
            ->where('c.id = :categoryId')
            ->andWhere('a.isValidatedAt = :validated')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('validated', true)
            ->orderBy('a.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countAllRecettes(): int
    {
        return $this->createQueryBuilder('r')                 // Crée un QueryBuilder pour l'entité 'r' (Recette)           
            ->select('COUNT(r.id)')                           // Sélectionne le nombre total de recettes (COUNT(r.id))
            ->getQuery()                                      // Exécute la requête et retourne le résultat sous forme de valeur d'un entier
            ->getSingleScalarResult();
    }

    public function searchByCategoryAndStep(string $query): array
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->leftJoin('r.categorie', 'c')
            ->leftJoin('r.etapes', 'e')
            ->where('r.isValidatedAt = :validated') // Ajout du filtre pour les recettes validées
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(c.nom)', 'LOWER(:query)'),
                    $qb->expr()->like('LOWER(e.description)', 'LOWER(:query)')
                )
            )
            ->setParameter('validated', true)
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recette[] Returns an array of Recette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recette
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
