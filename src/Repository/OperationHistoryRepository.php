<?php

namespace App\Repository;

use App\Entity\OperationHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OperationHistory>
 *
 * @method OperationHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperationHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperationHistory[]    findAll()
 * @method OperationHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationHistoryRepository extends ServiceEntityRepository
{
    public const COUNT = 5;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperationHistory::class);
    }

    public function findLatestOperations(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o')
            ->join('o.performedBy', 'u')
            ->leftJoin('o.pet', 'pet')
            ->orderBy('o.operationDate', 'DESC')
            ->setMaxResults(self::COUNT)
            ->getQuery()
            ->getResult();
    }
}
