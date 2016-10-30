<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MonthlyTicket
 *
 * @ORM\Table(name="monthly_ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MonthlyTicketRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MonthlyTicket
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Subscription", inversedBy="monthly_subscriptions")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private $subscription;



}