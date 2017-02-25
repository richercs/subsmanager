<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScheduleItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class ScheduleItemRepository extends EntityRepository
{
    public function getRunningScheduleItems() {
    }

}