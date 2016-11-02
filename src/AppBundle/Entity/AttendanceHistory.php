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
     * @var Schedule
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Schedule")
     * @ORM\JoinColumn(name="attended_session", referencedColumnName="id")
     */
    protected $attended_session;

    /**
     * @var UserAccount
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="attendee", referencedColumnName="id")
     */
    protected $attendee;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=2, name="is_monthly_ticket")
     */
    protected $isMonthlyTicket;


}