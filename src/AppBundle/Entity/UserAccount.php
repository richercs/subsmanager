<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAccount
 *
 * @ORM\Table(name="user_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserAccountRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserAccount
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
     * @var string
     *
     * @ORM\Column(length=140, name="first_name")
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(length=140, name="last_name")
     */
    private $last_name;

    /**
     * @var Subscription
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Subscription", inversedBy="subscription_owner")
     * @ORM\JoinColumn(name="owned_subscription_id", referencedColumnName="id")
     */
    private $owned_subscription;

    /**
     * @var Subscription
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Subscription", inversedBy="subscription_user")
     * @ORM\JoinColumn(name="used_subscription_id", referencedColumnName="id")
     */
    private $used_subscription;

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
     * @return UserAccount
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     * @return UserAccount
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     * @return UserAccount
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return Subscription
     */
    public function getOwnedSubscription()
    {
        return $this->owned_subscription;
    }

    /**
     * @param Subscription $owned_subscription
     * @return UserAccount
     */
    public function setOwnedSubscription($owned_subscription)
    {
        $this->owned_subscription = $owned_subscription;
        return $this;
    }

    /**
     * @return Subscription
     */
    public function getUsedSubscription()
    {
        return $this->used_subscription;
    }

    /**
     * @param Subscription $used_subscription
     * @return UserAccount
     */
    public function setUsedSubscription($used_subscription)
    {
        $this->used_subscription = $used_subscription;
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