<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ScheduleItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class ScheduleItemRepository extends EntityRepository
{
    public function getRunningScheduleItems() {
    }

//    FatalErrorException in DateTimeType.php line 53:
//    Error: Call to a member function format() on a non-object

//    public function getAllNotDeleted()
//    {
//        $result = $this->findBy(['deletedAt' => !null]);
//        $indexedResult = [];
//        if (empty($result)) {
//            return [];
//        } else {
//            /** @var ScheduleItem $scheduleItem */
//            foreach ($result as $scheduleItem) {
//                $indexedResult[$scheduleItem->getId()] = $scheduleItem;
//            }
//        }
//
//        return $indexedResult;
//    }

}