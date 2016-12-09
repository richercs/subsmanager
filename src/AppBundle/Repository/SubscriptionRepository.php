<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Subscription;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SubscriptionRepository extends EntityRepository
{
    public function getRunningSubs() {
        $return = new ArrayCollection();

        $sub1 = new Subscription();
        $sub1->setCreated(new \DateTime());

        $return->add($sub1);

        return $return;
    }
}
