<?php

namespace App\Repository;

use App\Entity\Pet;
use App\Model\Dto\PetSearchDto;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pet::class);
    }

    /**
     * @param PetSearchDto $petSearchDto
     * @return Paginator
     */
    public function findByFilter(PetSearchDto $petSearchDto, int $page, int $count = 10): Paginator
    {
        $page = $page ?? 1;
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.owner', 'o')
            ->orderBy('p.id', 'ASC');

        if ($petSearchDto->getName() !== null) {
            $queryBuilder
                ->andWhere('p.name LIKE LOWER(:name)')
                ->setParameter('name', '%' . $petSearchDto->getName() . '%');
        }

        if ($petSearchDto->getOwner() !== null) {
            $queryBuilder
                ->andWhere('LOWER(o.name) LIKE LOWER(:owner)')
                ->setParameter('owner', '%' . $petSearchDto->getOwner() . '%');
        }

        if ($petSearchDto->getDescription() !== null) {
            $queryBuilder
                ->andWhere('p.description LIKE LOWER(:description)')
                ->setParameter('description', '%' . $petSearchDto->getDescription() . '%');
        }

        $query = $queryBuilder->getQuery();

        $firstResult = ($page - 1) * $count;

        $query->setFirstResult($firstResult)
            ->setMaxResults($count);

        return new Paginator($query, true);
    }
}
