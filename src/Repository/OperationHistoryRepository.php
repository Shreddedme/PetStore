<?php

namespace App\Repository;

use App\Entity\OperationHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function findLatestOperationsByPet(): array
    {
        $sql = "
            SELECT
                operation_date,
                performed_by_id,
                pet_id
            FROM (
                     SELECT
                         operation_date,
                         performed_by_id,
                         pet_id,
                         ROW_NUMBER() OVER (PARTITION BY pet_id ORDER BY operation_date DESC) as row_num
                     FROM operation_history
                 ) AS ranked_operations
            WHERE row_num = 1;
        ";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('operation_date', 'operation_date');
        $rsm->addScalarResult('performed_by_id', 'performed_by_id');
        $rsm->addScalarResult('pet_id', 'pet_id');
        $query = $this->_em->createNativeQuery($sql, $rsm);

        return $query->getResult();
    }
}
