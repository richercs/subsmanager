<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $id;

    /**
     * @var EventSession
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EventSession")
     * @ORM\JoinColumn(name="event_session_id", referencedColumnName="id")
     */
    protected $event_session;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="subscription_in_use_id", referencedColumnName="id")
     */
    protected $subscription;

    // TODO: Beírás?

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

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
     * @return EventSession
     */
    public function getEventSession()
    {
        return $this->event_session;
    }

    /**
     * @param EventSession $event_session
     */
    public function setEventSession($event_session)
    {
        $this->event_session = $event_session;
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