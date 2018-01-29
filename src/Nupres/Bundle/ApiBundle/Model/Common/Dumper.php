<?php

namespace Nupres\Bundle\ApiBundle\Model\Common;

class Dumper
{
    public static function dump($log)
    {
        return dump('[DEBUGGER ' . date('YmdHis', time()) . ' ' . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) . '] ' . $log);
    }
}
