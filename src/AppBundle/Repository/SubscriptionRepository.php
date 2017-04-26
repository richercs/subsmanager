<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BreakEvent;
use AppBundle\Entity\Subscription;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SubscriptionRepository extends EntityRepository
{
    public function getRunningSubs() {
    }

    /**
     *
     *@param BreakEvent $breakEvent
     * @return array
     */
    public function getClashingSubscriptions($break_start, $break_due) {

        $query = $this->_em->createQuery('
                SELECT subscription
                FROM AppBundle\Entity\Subscription subscription 
                WHERE subscription.dueDate >= :break_start
                AND subscription.startDate <= :break_due
           ');

        $query->setParameters(array(
            'break_start' => $break_start,
            'break_due' => $break_due
        ));

        $result = $query->getResult();

        return $result;
    }
}
