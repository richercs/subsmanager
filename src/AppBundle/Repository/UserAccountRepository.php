<?php

namespace AppBundle\Repository;

use AppBundle\Entity\UserAccount;
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

    public function findLikeUserName($term) {

        $results = $this->createQueryBuilder('c')
            ->where('c.username LIKE :name')
            ->orderBy('c.username', 'ASC')
            ->setParameter('name', '%'.$term.'%')
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function getAllWithNullEmail()
    {
        $result = $this->findBy(['email' => null]);
        $indexedResult = [];
        if (empty($result)) {
            return [];
        } else {
            /** @var UserAccount $userAccount */
            foreach ($result as $userAccount) {
                $indexedResult[$userAccount->getId()] = $userAccount;
            }
        }

        return $indexedResult;
    }

}