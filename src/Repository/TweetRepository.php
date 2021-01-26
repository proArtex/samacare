<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TweetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    public function findAllByFilter(array $filter): array
    {
        $startTime = $filter['start'] ?? null;
        $endTime = $filter['end'] ?? null;
        $authorIds = isset($filter['authors'])
            ? explode(',', $filter['authors'])
            : [];

        $qb = $this->createQueryBuilder('t');

        if ($startTime) {
            $qb
                ->andWhere('t.timestamp > :start')
                ->setParameter('start', (new \DateTimeImmutable($startTime))->getTimestamp());
        }

        if ($endTime) {
            $qb
                ->andWhere('t.timestamp < :end')
                ->setParameter('end', (new \DateTimeImmutable($endTime))->getTimestamp());
        }

        if ($authorIds) {
            $qb
            ->andWhere('t.author IN (:author_ids)')
            ->setParameter('author_ids', $authorIds);
        }

        return $qb->getQuery()->getResult();
    }
}
