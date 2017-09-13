<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of UserRepository
 *
 * @author Julio
 */

class ProductRepository extends EntityRepository
{
    public function createFindOneById($id)
    {
        return $this->createQueryBuilder('p')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}