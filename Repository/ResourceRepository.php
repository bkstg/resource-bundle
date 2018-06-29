<?php

namespace Bkstg\ResourceBundle\Repository;

use Bkstg\ResourceBundle\Entity\Resource;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class ResourceRepository extends EntityRepository
{
    /**
     * Get the query to find all resources by group.
     *
     * @param  GroupInterface $group  The group to search in.
     * @param  boolean        $active The active state to search.
     * @return Query
     */
    public function findAllByGroupQuery(GroupInterface $group, bool $active = true): Query
    {
        $qb = $this->createQueryBuilder('r');
        return $qb
            ->join('r.groups', 'g')

            // Add conditions.
            ->andWhere($qb->expr()->eq('g', ':group'))
            ->andWhere($qb->expr()->eq('r.active', ':active'))

            // Add parameters.
            ->setParameter('group', $group)
            ->setParameter('active', $active)

            // Order by and get results.
            ->orderBy('r.pinned', 'DESC')
            ->addOrderBy('r.name', 'ASC')
            ->getQuery();
    }
}
