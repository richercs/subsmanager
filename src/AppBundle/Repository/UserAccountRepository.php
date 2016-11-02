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
    public function getAllWithNameShorterThen($length = 5)
    {
        $query = $this->_em->createQuery('
                SELECT user_acc
                FROM AppBundle\Entity\UserAccount user_acc 
                WHERE LENGTH(user_acc.first_name) <= :length
           ');

        $query->setParameter('length', (int) $length);

        $result = $query->getResult();

        return $result;
    }
}