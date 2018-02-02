<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Credentials
{
    public static function checked(ContainerInterface $container = null, $authorization = null)
    {
        $debugger = $container->get('nupres.dumper.service');

        // Escribiendo log en modo debugger
        $debugger::debugger(
            'GENERAL INFO',
            array(
                'CLASS'     => __CLASS__,
                'FILE'      => __FILE__,
                'METHOD'    => __METHOD__,
                'LINE'      => __LINE__
            )
        );

        $basicData = base64_decode($authorization);

        $credentials = explode(":", $basicData);

        if (count($credentials) == 2) {
            $user = $credentials[0];
            $pass = $credentials[1];
        }

        $credentialsConfig = $container->getParameter('nupres_config.api_key');

        // Escribiendo log en modo debugger
        $debugger::debugger(
            'VARS INFO',
            array(
                'authorization'                 => $authorization,
                'basicData'                     => $basicData,
                'user'                          => $user,
                'pass'                          => $pass,
                'credentialsConfig_user'        => $credentialsConfig['authorization']['provider_1']['user'],
                'credentialsConfig_pass'        => $credentialsConfig['authorization']['provider_1']['pass']
            )
        );

        if ($credentialsConfig['authorization']['provider_1']['user'] == $user && $credentialsConfig['authorization']['provider_1']['pass'] == $pass) {
            return true;
        }
    }
}
