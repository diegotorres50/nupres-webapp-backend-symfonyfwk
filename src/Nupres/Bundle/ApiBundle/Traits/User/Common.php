<?php

namespace Nupres\Bundle\ApiBundle\Traits\user;

trait Common
{
    // Metodo para hacer las validaciones de un alta de registro en usuarios
    public static function isValidRecord($record = [])
    {
        if (empty($record['userid'])) {
            throw new \Exception("Falta userid");
        }

        if (empty($record['username'])) {
            throw new \Exception("Falta username");
        }

        if (empty($record['usermail'])) {
            throw new \Exception("Falta usermail");
        }

        if (empty($record['userpass'])) {
            throw new \Exception("Falta userpass");
        }

        return true;
    }
}
