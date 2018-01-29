<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use \Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JWToken
{
    private static $secretKey;
    private static $encrypt;
    private static $iss;
    private static $aud;
    private static $uid;
    private static $exp;

    public function __construct(ContainerInterface $container = null)
    {
        try {
            self::$secretKey = $container->getParameter('nupres_config.jwt')['secret_key'];
            self::$encrypt = $container->getParameter('nupres_config.jwt')['algorithms'];
            self::$iss = $container->getParameter('nupres_config.jwt')['iss'];
            self::$aud = $container->getParameter('nupres_config.jwt')['aud'];
            self::$uid = $container->getParameter('nupres_config.jwt')['uid'];
            self::$exp = $container->getParameter('nupres_config.jwt')['exp'];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function encode($data = [])
    {
        try {
            $time = time();

            $token = array(
                'iat'   => $time, // La hora actual de emision, en segundos transcurridos desde el punto de inicio del tiempo UNIX.
                "iss"   => self::$iss, // Emisor del token
                "aud"   => self::$aud, // Publico al que va dirigo el token
                "uid"   => self::$uid, // El identificador único del usuario que accedió debe ser una string que contenga entre 1 y 36 caracteres.
                'data'  => $data
            );

            // Sino extuviera set el 'exp' key, en teoria el token no expira
            if (!empty(self::$exp)) {
                // Example: $time + (60*60), // Tiempo que expirará el token (+1 hora)
                $token['exp'] = self::$exp;
            }

            return JWT::encode($token, self::$secretKey);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function check($token)
    {
        if (empty($token)) {
            throw new Exception("Invalid token supplied.");
        }

        $decode = JWT::decode(
            $token,
            self::$secretKey,
            self::$encrypt
        );

        if ($decode->aud !== self::$aud) {
            throw new Exception("Invalid user logged in.");
        }
    }

    public static function decode($token, $secretKey)
    {
        return JWT::decode(
            $token,
            $secretKey,
            self::$encrypt
        )->data;
    }
}
