<?php


namespace AppBundle\Repository;


use AppBundle\Entity\AnnouncedSession;
use Doctrine\ORM\EntityRepository;

class AnnouncedSessionRepository extends EntityRepository
{

    public function getLastThirty()
    {
        $query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session 
                ORDER BY announced_session.createdAt DESC
           ');

        $query->setMaxResults(30);

        $result = $query->getResult();

        return $result;
    }

    public function getBetweenDates($stats_start, $stats_due)
    {
        $query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session 
                WHERE announced_session.createdAt >= :stats_start
                AND announced_session.createdAt <= :stats_due
                ORDER BY announced_session.createdAt DESC
           ');

        $query->setParameters(array(
            'stats_start' => $stats_start,
            'stats_due' => $stats_due
        ));

        $result = $query->getResult();

        return $result;
    }

    public function getLastThirtyFilteredScheduleItem($statsScheduleItem)
    {
        $query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session
                WHERE announced_session.scheduleItem = :filter
                ORDER BY announced_session.createdAt DESC
           ');

        $query->setParameters(array(
            'filter' => $statsScheduleItem
        ));

        $query->setMaxResults(30);

        $result = $query->getResult();

        return $result;
    }

    public function getBetweenDatesFilteredScheduleItem($stats_start, $stats_due, $statsScheduleItem)
    {
        $query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session 
                WHERE announced_session.createdAt >= :stats_start
                AND announced_session.createdAt <= :stats_due
                AND announced_session.scheduleItem = :filter
           ');

        $query->setParameters(array(
            'stats_start' => $stats_start,
            'stats_due' => $stats_due,
            'filter' => $statsScheduleItem
        ));

        $result = $query->getResult();

        return $result;
    }

    /**
     * Save the announced session to the database
     *
     * @param AnnouncedSession $announcedSession
     */
    public function save(AnnouncedSession $announcedSession)
    {
        $this->_em->persist($announcedSession);
        $this->_em->flush();
    }
}