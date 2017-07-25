<?php

namespace AppBundle\Repository;

use AppBundle\Entity\SessionEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SessionEventRepository extends EntityRepository
{
    public function getRunningSessionEvents() {
    }

    public function getSessionsBetweenDates($stats_start, $stats_due) {

        $query = $this->_em->createQuery('
                SELECT sessionevent
                FROM AppBundle\Entity\SessionEvent sessionevent 
                WHERE sessionevent.sessionEventDate >= :stats_start
                AND sessionevent.sessionEventDate <= :stats_due
           ');

        $query->setParameters(array(
            'stats_start' => $stats_start,
            'stats_due' => $stats_due
        ));

        $result = $query->getResult();

        return $result;
    }

    public function getLastThirtySessions() {

        $query = $this->_em->createQuery('
                SELECT sessionevent
                FROM AppBundle\Entity\SessionEvent sessionevent 
                ORDER BY sessionevent.id DESC
           ');

        $query->setMaxResults(30);

        $result = $query->getResult();

        return $result;
    }

    public function getSessionsBetweenDatesFilteredScheduleItem($stats_start, $stats_due, $statsScheduleItem) {

        $query = $this->_em->createQuery('
                SELECT sessionevent
                FROM AppBundle\Entity\SessionEvent sessionevent 
                WHERE sessionevent.sessionEventDate >= :stats_start
                AND sessionevent.sessionEventDate <= :stats_due
                AND sessionevent.scheduleItem = :filter
           ');

        $query->setParameters(array(
            'stats_start' => $stats_start,
            'stats_due' => $stats_due,
            'filter' => $statsScheduleItem
        ));

        $result = $query->getResult();

        return $result;
    }

    public function getLastThirtySessionsFilteredScheduleItem($statsScheduleItem) {

        $query = $this->_em->createQuery('
                SELECT sessionevent
                FROM AppBundle\Entity\SessionEvent sessionevent
                WHERE sessionevent.scheduleItem = :filter
                ORDER BY sessionevent.id DESC
           ');

        $query->setParameters(array(
            'filter' => $statsScheduleItem
        ));

        $query->setMaxResults(30);

        $result = $query->getResult();

        return $result;
    }
}