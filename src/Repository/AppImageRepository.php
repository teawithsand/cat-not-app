<?php

namespace App\Repository;

use App\Entity\AppImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppImage>
 *
 * @method AppImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppImage[]    findAll()
 * @method AppImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppImage::class);
    }

//    /**
//     * @return AppImage[] Returns an array of AppImage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AppImage
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
