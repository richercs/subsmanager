<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AttendanceHistory
 *
 * @ORM\Table(name="attendance_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttendanceHistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */

class AttendanceHistory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var SessionEvent
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SessionEvent", inversedBy="attendees")
     * @ORM\JoinColumn(name="session_event_id", referencedColumnName="id")
     */
    protected $session_event;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="attendee_id", referencedColumnName="id")
     */
    protected $attendee;

    /**
     * @var Subscription
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subscription")
     * @ORM\JoinColumn(name="subscription_in_use_id", referencedColumnName="id")
     */
    protected $subscription;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    public function __toString()
    {
        return (string) $this->getSessionEvent();
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
     * @return SessionEvent
     */
    public function getSessionEvent()
    {
        return $this->session_event;
    }

    /**
     * @param SessionEvent $session_event
     */
    public function setSessionEvent($session_event)
    {
        $this->session_event = $session_event;
    }

    /**
     * @return UserAccount
     */
    public function getAttendee()
    {
        return $this->attendee;
    }

    /**
     * @param UserAccount $attendee
     */
    public function setAttendee($attendee)
    {
        $this->attendee = $attendee;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param Subscription $subscription
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
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
     * @return AttendanceHistory
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
     * @return AttendanceHistory
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