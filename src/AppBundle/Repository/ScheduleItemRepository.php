<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScheduleItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class ScheduleItemRepository extends EntityRepository
{
    public function getOrderedScheduleItems() {

        $query = $this->_em->createQuery('
                SELECT item
                FROM AppBundle\Entity\ScheduleItem item 
                ORDER BY item.scheduledDay, item.scheduledStartTime
           ');

        $result = $query->getResult();

        return $result;
    }



}