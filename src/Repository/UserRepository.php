<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    public function findAllNew() {
        return $this->createQueryBuilder('u')
            ->where('u.status = \'NEW\'')
            ->orderBy('u.created_at', 'DESC')
            ->getQuery()->getResult();
    }

    public function searchByLastnameOrFirstname($value) {
        $qb = $this->createQueryBuilder('u');
        return $qb->andWhere($qb->expr()->like('u.lastname', '?1'))
            ->orWhere($qb->expr()->like('u.firstname', '?1'))
            ->setParameter(1, '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllCustom() {
        return $this->createQueryBuilder('u')
            ->where('u.status != \'NEW\'')
            ->orderBy('u.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
