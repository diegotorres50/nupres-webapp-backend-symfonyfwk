<?php

namespace Nupres\Bundle\ApiBundle\Entity;

// https://symfony.com/doc/current/best_practices/configuration.html
// Creating a configuration option for a value that you are never going to configure just isn't
// necessary. Our recommendation is to define these values as constants in your application.

class Entities
{
    const TABLE_PACIENTES               = 'pacientes';
    const VIEW_PACIENTES_ACTIVOS        = 'pacientes_activos';

    public static function getDbEntities()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}
