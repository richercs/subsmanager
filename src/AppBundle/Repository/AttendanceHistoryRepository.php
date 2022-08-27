<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AttendanceHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class AttendanceHistoryRepository extends EntityRepository
{
    public function getRunningAttendances() {
    }

    public function getAttendancesOfSubscriptionOrderedBySessionEventDate($subscription) {

	    $query = $this->_em->createQuery('
	            SELECT attendancehistory
	            FROM AppBundle\Entity\AttendanceHistory attendancehistory
	            LEFT JOIN AppBundle\Entity\SessionEvent sessionevent
	            WITH attendancehistory.session_event = sessionevent
	            WHERE attendancehistory.subscription = :subscription
	            ORDER BY sessionevent.sessionEventDate DESC
	       ');

	    $query->setParameters(array(
	        'subscription' => $subscription
	    ));

	    return $query->getResult();
	}

}
