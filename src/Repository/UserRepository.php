<?php

namespace App\Repository;

use App\Model\Dto\UserRequestDto;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public const CACHE_EXPIREDTIME = 3600;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUsers(UserRequestDto $userRequestDto): Paginator
    {
        $page = $userRequestDto->getPage() ?? 1;
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('u');

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $userRequestDto->getCount();

        $query->setFirstResult($firstResult)
            ->setMaxResults($userRequestDto->getCount())
            ->enableResultCache(self::CACHE_EXPIREDTIME, 'user_filter_id');

        return new Paginator($query, true);
    }
}
