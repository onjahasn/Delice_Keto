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
            ->join('a.categorie', 'c') // Utilisez 'a.categorie' (nom exact de la relation dans Recette)
            ->where('c.id = :categoryId') // Filtrer par ID de la catégorie
            ->setParameter('categoryId', $categoryId)
            ->orderBy('a.titre', 'ASC') // Trier par titre (facultatif)
            ->getQuery()
            ->getResult();
    }

    public function countAllRecettes(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function searchByCategoryAndStep(string $query): array
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->leftJoin('r.categorie', 'c')   // Jointure avec la catégorie
            ->leftJoin('r.etapes', 'e')      // Jointure avec les étapes
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(c.nom)', 'LOWER(:query)'),         // Catégorie
                    $qb->expr()->like('LOWER(e.description)', 'LOWER(:query)') // Étape
                )
            )
            ->setParameter('query', '%' . $query . '%') // Recherche partielle
            ->orderBy('r.createdAt', 'DESC')           // Trie par date de création (optionnel)
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
