<?php

namespace SubsmanagerUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SubsmanagerUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}