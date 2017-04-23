<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserContactRepository extends EntityRepository
{
    public function getAllWithNameShorterThen($length = 5)
    {
        $query = $this->_em->createQuery('
                SELECT user_contact
                FROM AppBundle\Entity\UserContact user_contact 
                WHERE LENGTH(user_contact.first_name) <= :length
           ');

        $query->setParameter('length', (int) $length);

        $result = $query->getResult();

        return $result;
    }

    public function getPendingCount()
    {
        return $this->_em->createQuery(
            '
                SELECT COUNT(u.id)
                FROM AppBundle\Entity\UserContact u
                WHERE u.deletedAt IS NULL
            '
        )->getArrayResult()[0][1];

    }

}