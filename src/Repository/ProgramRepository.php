<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

//    /**
//     * @return Program[] Returns an array of Program objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Program
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findTopRatedPrograms(int $limit = 5): array
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.reviews', 'r') 
        ->groupBy('p.id') 
        ->orderBy('AVG(r.rating)', 'DESC') 
        ->setMaxResults($limit) 
        ->getQuery()
        ->getResult();
}

public function findProgramsByCoach($coach): array
{
    return $this->createQueryBuilder('p')
        ->where('p.coach = :coach')
        ->setParameter('coach', $coach)
        ->getQuery()
        ->getResult();
}
public function findLatestPrograms($limit = 5): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

public function findLastProgramsByUser($user): array
{
    return $this->createQueryBuilder('p')
        ->where(':user MEMBER OF p.users')  
        ->setParameter('user', $user)
        ->orderBy('p.id', 'DESC')  
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
}

}
