<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Plan;
use App\Entity\PlanRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanRow>
 *
 * @method PlanRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanRow[]    findAll()
 * @method PlanRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanRow::class);
    }

    public function add(PlanRow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanRow $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGreaterEqualsThanDate(Plan $plan, \DateTimeInterface $startDate)
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.date >= :start')
            ->andWhere('pr.plan = :plan')
            ->setParameter('start', $startDate)
            ->setParameter('plan', $plan)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return PlanRow[] Returns an array of PlanRow objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlanRow
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
