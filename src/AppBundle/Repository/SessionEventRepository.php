<?php

namespace AppBundle\Repository;

use AppBundle\Entity\SessionEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SessionEventRepository extends EntityRepository
{
    public function getRunningSessionEvents() {
    }

}