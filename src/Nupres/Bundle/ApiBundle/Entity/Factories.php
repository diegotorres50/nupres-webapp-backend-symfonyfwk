<?php

namespace Nupres\Bundle\ApiBundle\Entity;

// https://symfony.com/doc/current/best_practices/configuration.html
// Creating a configuration option for a value that you are never going to configure just isn't
// necessary. Our recommendation is to define these values as constants in your application.

class Factories
{
    const DEMO01          = 'nupres_dev_demo01';
    const DEMO02          = 'nupresco_dev_demo01';

    public static function getFactoriesMap()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
