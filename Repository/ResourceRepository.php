<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class ResourceRepository extends EntityRepository
{
    /**
     * Get the query to find all resources by group.
     *
     * @param GroupInterface $group  The group to search in.
     * @param bool           $active The active state to search.
     *
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
