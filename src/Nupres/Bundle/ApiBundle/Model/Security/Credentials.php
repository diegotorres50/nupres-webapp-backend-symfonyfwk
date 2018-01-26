<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Credentials
{
    public static function checked(ContainerInterface $container = null, $authorization = null)
    {
        $basicData = base64_decode($authorization);

        $credentials = explode(":", $basicData);

        if (count($credentials) == 2) {
            $user = $credentials[0];
            $pass = $credentials[1];
        }

        $credentialsConfig = $container->getParameter('nupres_config.api_key');

        if ($credentialsConfig['authorization']['user'] == $user && $credentialsConfig['authorization']['pass'] == $pass) {
            return true;
        }
    }
}
