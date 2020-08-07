<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SessionEvent
 *
 * @ORM\Table(name="session_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionEventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SessionEvent
{

    public function __construct() {
        $this->attendees = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ScheduleItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ScheduleItem")
     * @ORM\JoinColumn(name="schedule_item_id", referencedColumnName="id", nullable=false)
     */
    protected $scheduleItem;

    /**
     * @var AnnouncedSession
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\AnnouncedSession")
     * @ORM\JoinColumn(name="announced_session_id", referencedColumnName="id", nullable=true)
     */
    protected $announcedSessionId;

    /**
     * @ORM\Column(name="session_event_date", type="datetime", nullable = false)
     */
    protected $sessionEventDate;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AttendanceHistory", mappedBy="session_event", cascade={"remove","persist"})
     */
    protected $attendees;

    /**
     * @var int
     * @ORM\Column(name="session_fee_numbers_sold", type="integer", nullable = true)
     */
    protected $sessionFeeNumbersSold;

    /**
     * @var int
     * @ORM\Column(name="session_fee_revenue_sold", type="integer", nullable = true)
     */
    protected $sessionFeeRevenueSold;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    /**
     * @var int $revenue
     */
    protected $revenue;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getScheduleItem(). ' ' . $this->getSessionEventDate()->format('Y.m.d. H:i') . ' [' . $this->getId() . ']';
    }

    public function addAttendee(AttendanceHistory $attendance)
    {
        $attendance->setSessionEvent($this);

        $this->attendees->add($attendance);
    }

    public function removeAttendee(AttendanceHistory $attendance)
    {
        $this->attendees->removeElement($attendance);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ScheduleItem
     */
    public function getScheduleItem()
    {
        return $this->scheduleItem;
    }

    /**
     * @param ScheduleItem $scheduleItem
     */
    public function setScheduleItem($scheduleItem)
    {
        $this->scheduleItem = $scheduleItem;
    }

    /**
     * @return AnnouncedSession
     */
    public function getAnnouncedSessionId()
    {
        return $this->announcedSessionId;
    }

    /**
     * @param AnnouncedSession $announcedSessionId
     */
    public function setAnnouncedSessionId($announcedSessionId)
    {
        $this->announcedSessionId = $announcedSessionId;
    }

    /**
     * @return mixed
     */
    public function getSessionEventDate()
    {
        return $this->sessionEventDate;
    }

    /**
     * @param mixed $sessionEventDate
     */
    public function setSessionEventDate($sessionEventDate)
    {
        $this->sessionEventDate = $sessionEventDate;
    }

    /**
     * @return string
     */
    public function getSessionEventDateString() {
        return $this->getSessionEventDate()->format('Y.m.d. H:i');
    }

    /**
     * @return mixed
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param mixed $attendees
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;
    }

    /**
     * @return int
     */
    public function getAttendeeFullCount()
    {
        return $this->attendees->count() + $this->sessionFeeNumbersSold;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
    }

    /**
     * @return mixed
     */
    public function getSessionFeeNumbersSold()
    {
        return $this->sessionFeeNumbersSold;
    }

    /**
     * @param mixed $sessionFeeNumbersSold
     */
    public function setSessionFeeNumbersSold($sessionFeeNumbersSold)
    {
        $this->sessionFeeNumbersSold = $sessionFeeNumbersSold;
    }

    /**
     * @return mixed
     */
    public function getSessionFeeRevenueSold()
    {
        return $this->sessionFeeRevenueSold;
    }

    /**
     * @param mixed $sessionFeeRevenueSold
     */
    public function setSessionFeeRevenueSold($sessionFeeRevenueSold)
    {
        $this->sessionFeeRevenueSold = $sessionFeeRevenueSold;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return SessionEvent
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return SessionEvent
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdated(new \DateTime('now'));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime('now'));
        }
    }
}