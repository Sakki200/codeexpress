<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }
    public function findByNote(string $note): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.note = :note')
            ->setParameter('note', $note)
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByIdAndAuthor(int $id, User $author): ?Like
    {
        return $this->createQueryBuilder('l')
            ->where('l.note = :id')
            ->andWhere('l.author = :author')
            ->setParameter('id', $id)
            ->setParameter('author', $author)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
