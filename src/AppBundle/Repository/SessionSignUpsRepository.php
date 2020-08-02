<?php


namespace AppBundle\Repository;


use AppBundle\Entity\SessionSignUp;
use Doctrine\ORM\EntityRepository;

class SessionSignUpsRepository extends EntityRepository
{
    /**
     * Remove the session sign up from database
     *
     * @param SessionSignUp $signee
     */
    public function delete(SessionSignUp $signee)
    {
        $this->_em->remove($signee);
        $this->_em->flush();
    }
}