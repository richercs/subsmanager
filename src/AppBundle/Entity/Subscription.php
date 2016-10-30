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
        $this->monthly_subscriptions = new ArrayCollection();
        $this->casual_subscriptionss = new ArrayCollection();
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
     * @var UserAccount
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserAccount", mappedBy="owned_subscription")
     * @ORM\JoinColumn(name="subscription_owner_id", referencedColumnName="id")
     */
    protected $subscription_owner;

    /**
     * @var UserAccount
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserAccount", mappedBy="used_subscription")
     * @ORM\JoinColumn(name="subscription_user_id", referencedColumnName="id")
     */
    protected $subscription_user;

    /**
     * @var MonthlyTicket
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MonthlyTicket", mappedBy="subscription")
     * @ORM\JoinColumn(name="monthly_subscriptions", referencedColumnName="id")
     */
    private $monthly_subscriptions;

    /**
     * @var CasualTicket
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CasualTicket", mappedBy="subscription")
     * @ORM\JoinColumn(name="casual_subscriptions", referencedColumnName="id")
     */
    private $casual_subscriptions;

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
     * @return Subscription
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UserAccount
     */
    public function getSubscriptionOwner()
    {
        return $this->subscription_owner;
    }

    /**
     * @param UserAccount $subscription_owner
     * @return Subscription
     */
    public function setSubscriptionOwner($subscription_owner)
    {
        $this->subscription_owner = $subscription_owner;
        return $this;
    }

    /**
     * @return UserAccount
     */
    public function getSubscriptionUser()
    {
        return $this->subscription_user;
    }

    /**
     * @param UserAccount $subscription_user
     * @return Subscription
     */
    public function setSubscriptionUser($subscription_user)
    {
        $this->subscription_user = $subscription_user;
        return $this;
    }

    /**
     * @return MonthlyTicket
     */
    public function getMonthlySubscriptions()
    {
        return $this->monthly_subscriptions;
    }

    /**
     * @param MonthlyTicket $monthly_subscriptions
     * @return Subscription
     */
    public function setMonthlySubscriptions($monthly_subscriptions)
    {
        $this->monthly_subscriptions = $monthly_subscriptions;
        return $this;
    }

    /**
     * @return CasualTicket
     */
    public function getCasualSubscriptions()
    {
        return $this->casual_subscriptions;
    }

    /**
     * @param CasualTicket $casual_subscriptions
     * @return Subscription
     */
    public function setCasualSubscriptions($casual_subscriptions)
    {
        $this->casual_subscriptions = $casual_subscriptions;
        return $this;
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
     * @return Guest
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
     * @return Guest
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
