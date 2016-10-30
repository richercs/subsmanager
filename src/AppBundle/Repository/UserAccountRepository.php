<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 10/29/16
 * Time: 11:44 AM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserAccountRepository extends EntityRepository
{
    public function getAllWithNameShorterThen($length = 100)
    {
        $query = $this->_em->createQuery('
                SELECT guest
                FROM AppBundle\Entity\Guest guest 
                WHERE LENGTH(guest.name) <= :length
           ');

        $query->setParameter('length', (int) $length);

        $result = $query->getResult();

        return $result;
    }
}