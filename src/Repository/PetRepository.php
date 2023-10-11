<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Model\Dto\PetCombinedDto;
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
     * @param PetCombinedDto $petCombinedDto
     * @return Paginator
     */
    public function findByFilter(PetCombinedDto $petCombinedDto): Paginator
    {
        $page = $petCombinedDto->getPage() ?? 1;
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.owner', 'o')
            ->orderBy('p.' . $petCombinedDto->getSortBy(), $petCombinedDto->getSortDirection());

        if ($petCombinedDto->getName() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . $petCombinedDto->getName() . '%');
        }

        if ($petCombinedDto->getOwner() !== null) {
            $queryBuilder
                ->andWhere('LOWER(o.name) LIKE LOWER(:owner)')
                ->setParameter('owner', '%' . $petCombinedDto->getOwner() . '%');
        }

        if ($petCombinedDto->getPetDescription() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.description) LIKE LOWER(:description)')
                ->setParameter('description', '%' . $petCombinedDto->getPetDescription() . '%');
        }

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $petCombinedDto->getPerPage();

        $query->setFirstResult($firstResult)
            ->setMaxResults($petCombinedDto->getPerPage())
            ->enableResultCache(self::CACHE_EXPIREDTIME, 'filters_id');

        return new Paginator($query, true);
    }
}
