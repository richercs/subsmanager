<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BreakEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class BreakEventRepository extends EntityRepository
{
    public function getRunningBreaks() {
        $return = new ArrayCollection();

        $break1 = new BreakEvent();
        $break1->setCreated(new \DateTime());

        $return->add($break1);

        return $return;
    }

}