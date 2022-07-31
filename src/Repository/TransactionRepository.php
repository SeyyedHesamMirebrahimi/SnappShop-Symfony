<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('t')

            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws Exception
     */
    public function getMostRecent($minutes ):array
    {
//        $minutes = 1;
        $now = new \DateTime('-'.$minutes.'Minutes');
        return $this->createQueryBuilder('t')
            ->join('t.cardNumber' , 'cardNumber')
            ->join('cardNumber.accountNumber' , 'accountNumber')
            ->join('accountNumber.user' , 'user')
            ->select('count(user.id) as total , user.id as userId , t.description')
            ->andWhere('t.createdAt >= :time')
            ->setParameter('time', $now->format('Y-m-d H:i:s'))
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getUserTransactions($userId , $limit): array|int|string
    {
        return $this->createQueryBuilder('t')
            ->join('t.cardNumber' , 'cardNumber')
            ->join('cardNumber.accountNumber' , 'accountNumber')
            ->join('accountNumber.user' , 'user')
            ->andWhere('user.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->select('user.name , t.description, t.price')
            ->getQuery()
            ->getArrayResult();
    }
}
