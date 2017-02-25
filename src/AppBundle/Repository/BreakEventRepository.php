<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BreakEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class BreakEventRepository extends EntityRepository
{
    public function getRunningBreaks() {
    }

}