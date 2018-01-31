<?php

namespace Nupres\Bundle\ApiBundle\Model\Common;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Dumper
{
    private static $_request;

    public function __construct(ContainerInterface $container = null)
    {
        try {
            self::$_request = $container->get('nupres.request.service');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function dump($log)
    {
        return dump('[DEBUGGER ' . date('YmdHis', time()) . ' ' . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) . '] ' . $log);
    }

    public static function debugger($log = '', $context = [])
    {
        if (boolval(self::$_request->getQueryString('debugger'))) {
            echo '[DEBUGGER ' . date('YmdHis', time()) . ' ' . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) . '] ';
            if (!empty($log)) {
                echo $log . '<br>';
            }
            if (!empty($context)) {
                print_r($context);
            }
        }
    }
}
