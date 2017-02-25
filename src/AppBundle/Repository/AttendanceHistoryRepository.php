<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AttendanceHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class AttendanceHistoryRepository extends EntityRepository
{
    public function getRunningAttendances() {
    }

}