<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks
 */

class Schedule
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
     * @var UserAccount
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="trainer", referencedColumnName="id")
     */
    protected $trainer;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $scheduledDate;

    /**
     * @var string
     *
     * @ORM\Column(length=140, name="location")
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(length=140, name="training_type")
     */
    private $training_type;


}