<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScheduleItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class ScheduleItemRepository extends EntityRepository
{
    public function getRunningScheduleItems() {
        $return = new ArrayCollection();

        $schedule_item1 = new ScheduleItem();
        $schedule_item1->setCreated(new \DateTime());

        $return->add($schedule_item1);

        return $return;
    }

}