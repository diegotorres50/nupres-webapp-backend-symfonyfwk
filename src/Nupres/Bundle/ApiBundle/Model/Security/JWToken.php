<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use \Firebase\JWT\JWT;

// https://symfony.com/doc/current/components/using_components.html
// How to Install and Use the Symfony Components
// Para poder user guzzle client desde el vendor del bundle
//require_once __DIR__.'/../vendor/autoload.php';

class JWToken
{
    private static $secretKey = 'Sdw1s9x8@';
    private static $encrypt = ['HS256'];
    private static $iss = 'nupres.com.co';
    private static $aud = 'nupres.com.co';
    private static $uid = 'nupres.com.co';

    public static function encode($data = [])
    {
        try {
            $time = time();

            $token = array(
                'iat'   => $time, // La hora actual de emision, en segundos transcurridos desde el punto de inicio del tiempo UNIX.
                'exp'   => $time + (60*60), // Tiempo que expirará el token (+1 hora)
                "iss"   => self::$iss, // Emisor del token
                "aud"   => self::$aud, // Publico al que va dirigo el token
                "uid"   => self::$uid, // El identificador único del usuario que accedió debe ser una string que contenga entre 1 y 36 caracteres.
                'data'  => $data
            );

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
            $secretKey,
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
