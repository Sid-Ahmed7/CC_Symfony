<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findSessionsByCoach($coach): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.program', 'p')
            ->where('p.coach = :coach')
            ->setParameter('coach', $coach)
            ->orderBy('s.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSessionsByUser($user, $limit = 2): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.sessionHistories', 'sh')
            ->innerJoin('sh.member', 'm')
            ->where('m = :user')
            ->setParameter('user', $user)
            ->orderBy('s.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findLatestSessions($limit = 5): array
{
    return $this->createQueryBuilder('s')
        ->orderBy('s.id', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}
}
