<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Model\Dto\PetRequestDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pet>
 *
 * @method Pet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pet[]    findAll()
 * @method Pet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetRepository extends ServiceEntityRepository
{
    public const CACHE_EXPIREDTIME = 3600;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * @param PetRequestDto $petRequestDto
     * @return Paginator
     */
    public function findByFilter(PetRequestDto $petRequestDto): Paginator
    {
        $page = $petRequestDto->getPage() ?? 1;
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.owner', 'o')
            ->orderBy('p.' . $petRequestDto->getSortBy(), $petRequestDto->getSortDirection());

        if ($petRequestDto->getName() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . $petRequestDto->getName() . '%');
        }

        if ($petRequestDto->getOwner() !== null) {
            $queryBuilder
                ->andWhere('LOWER(o.name) LIKE LOWER(:owner)')
                ->setParameter('owner', '%' . $petRequestDto->getOwner() . '%');
        }

        if ($petRequestDto->getPetDescription() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.description) LIKE LOWER(:description)')
                ->setParameter('description', '%' . $petRequestDto->getPetDescription() . '%');
        }

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $petRequestDto->getPerPage();

        $query->setFirstResult($firstResult)
            ->setMaxResults($petRequestDto->getPerPage())
            ->enableResultCache(self::CACHE_EXPIREDTIME, 'filters_id');

        return new Paginator($query, true);
    }
}
