<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Model\Dto\PetSearchDto;
use App\Model\Dto\PetSortDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * @param PetSearchDto $petSearchDto
     * @return Paginator
     */
    public function findByFilter(PetSearchDto $petSearchDto, int $count = 10): Paginator
    {
        $page = $petSearchDto->getPage() ?? 1;
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.owner', 'o')
            ->orderBy('p.id', 'ASC');

        if ($petSearchDto->getName() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . $petSearchDto->getName() . '%');
        }

        if ($petSearchDto->getOwner() !== null) {
            $queryBuilder
                ->andWhere('LOWER(o.name) LIKE LOWER(:owner)')
                ->setParameter('owner', '%' . $petSearchDto->getOwner() . '%');
        }

        if ($petSearchDto->getDescription() !== null) {
            $queryBuilder
                ->andWhere('LOWER(p.description) LIKE LOWER(:description)')
                ->setParameter('description', '%' . $petSearchDto->getDescription() . '%');
        }

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $count;

        $query->setFirstResult($firstResult)
            ->setMaxResults($count);

        return new Paginator($query, true);
    }

    public function findAllSorted(PetSortDto $petSortDto): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.' . $petSortDto->getSortBy(), $petSortDto->getSortDirection());

        return $qb->getQuery()->getResult();
    }
}
