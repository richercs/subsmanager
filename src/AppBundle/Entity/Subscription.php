<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_monthly_ticket", type="boolean")
     *
     */
    protected $isMonthlyTicket;

    /**
     * @ORM\Column(name="date_start_date", type="datetime", nullable = false)
     */
    protected $start_date;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    protected $owner;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=false)
     */
    protected $buyer;

    /**
     * @var int
     * @ORM\Column(name="price", type="integer")
     */
    protected $price;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Owner name: ' . $this->getOwner()
        . ' Owner email: ' . $this->getOwner()->getEmail()
        . ' Buyer: ' . $this->getBuyer() . ' '
        . $this->isIsMonthlyTicket() . ' '
        . ' (' . $this->getStartDate()->format('Y-m-d H:i') .')'
        . ' [' . $this->getId() . ']'
        . ' {' . $this->getStatus() . '}';
    }

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
     * @return boolean
     */
    public function isIsMonthlyTicket()
    {
        return $this->isMonthlyTicket;
    }

    /**
     * @param boolean $isMonthlyTicket
     */
    public function setIsMonthlyTicket($isMonthlyTicket)
    {
        $this->isMonthlyTicket = $isMonthlyTicket;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return UserAccount
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param UserAccount $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return UserAccount
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @param mixed $buyer
     */
    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $expiresAt = $this->getStartDate()->add(new \DateInterval('P1M'));
        $now = new \DateTime();

        if ($expiresAt < $now) {
            return 'EXPIRED';
        }

        return 'ACTIVE';
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
