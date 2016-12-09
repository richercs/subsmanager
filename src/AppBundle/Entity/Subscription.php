<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Subscription
{

    public function __construct()
    {

    }

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var TicketData
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\TicketData", mappedBy="subscription")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    protected $ticket;

    // TODO: Kezdet?

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="attendee_id", referencedColumnName="id")
     */
    protected $attendee;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return TicketData
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param TicketData $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
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
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
