<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CasualTicket
 *
 * @ORM\Table(name="casual_ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CasualTicketRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CasualTicket
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
     * @var Subscription
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subscription", inversedBy="casual_subscriptions")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private $subscription;



}