<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TweetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    public function findAllByFilterForUser(array $filter, ?User $user): array
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

        $this->filterPrivateTweets($qb, $user);

        return $qb->getQuery()->getResult();
    }

    public function findForUser(int $id, ?User $user): ?Tweet
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->andWhere('t.id = :id')
            ->setParameter('id', $id);

        $this->filterPrivateTweets($qb, $user);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function filterPrivateTweets(QueryBuilder $qb, ?User $user): void
    {
        $orX = $qb->expr()->orX();
        $orX->add($qb->expr()->eq('t.isPrivate', ':is_private'));
        $qb->setParameter('is_private', false);

        if ($user) {
            $orX->add($qb->expr()->eq('t.author', ':author_id'));
            $orX->add($qb->expr()->isMemberOf(':follower_id', 'a.followers'));

            $qb
                ->join('t.author', 'a')
                ->setParameter('author_id', $user->getId())
                ->setParameter('follower_id', $user->getId());
        }

        $qb->andWhere($orX);
    }
}
