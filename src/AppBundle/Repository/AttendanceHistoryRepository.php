<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AttendanceHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class AttendanceHistoryRepository extends EntityRepository
{
    public function getRunningAttendances() {
        $return = new ArrayCollection();

        $attendance1 = new AttendanceHistory();
        $attendance1->setCreated(new \DateTime());

        $return->add($attendance1);

        return $return;
    }

}