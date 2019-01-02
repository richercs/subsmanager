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
	            SELECT attendancehistroy
	            FROM AppBundle\Entity\AttendanceHistory attendancehistroy 
	            LEFT JOIN AppBundle\Entity\SessionEvent sessionevent
	            WITH attendancehistroy.session_event = sessionevent
	            WHERE attendancehistroy.subscription = :subscription
	            ORDER BY sessionevent.sessionEventDate DESC
	       ');

	    $query->setParameters(array(
	        'subscription' => $subscription
	    ));

	    $result = $query->getResult();

	    return $result;
	}

}