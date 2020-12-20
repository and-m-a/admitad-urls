<?php

namespace App\Repository;

use App\Entity\URL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method URL|null find($id, $lockMode = null, $lockVersion = null)
 * @method URL|null findOneBy(array $criteria, array $orderBy = null)
 * @method URL[]    findAll()
 * @method URL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class URLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, URL::class);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getUserStatistics(array $params): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select("count('*') as count");

        if ($params['group_by_user'] ?? false) {
            $qb->addSelect('u.user_id')
                ->groupBy('u.user_id');
        }

        // Group by created_at is useless, statistics can be grouped by Date(created_at), but Date(created_at) is not working on QueryBuilder. Custom Query can be written
        if ($params['group_by_date'] ?? false) {
            $qb->addSelect("u.created_at")
                ->groupBy("u.created_at");
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param string $shortUrl
     * @return string
     */
    public function getBaseUrl(string $shortUrl): ?string
    {
        $urls = $this->createQueryBuilder('u')
            ->select("u.base")
            ->where('u.short = :short')
            ->setParameter('short', $shortUrl)
            ->setMaxResults(1)
            ->getQuery()
            ->execute();

        return $urls[0]['base'] ?? null;
    }
}
