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

    public function getLastFiftySessions() {

        $query = $this->_em->createQuery('
                SELECT sessionevent
                FROM AppBundle\Entity\SessionEvent sessionevent 
                ORDER BY sessionevent.id DESC
           ');

        $query->setMaxResults(50);

        $result = $query->getResult();

        return $result;
    }

}