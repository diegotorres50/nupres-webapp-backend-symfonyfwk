<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Auth
{
    private $_dbClient;

    private $_container;

    private $_debugger;

    const LOGIN_QUERY = 'SELECT * FROM usuarios WHERE (user_id = \'%s\' or user_mail = \'%s\') AND user_status = \'ACTIVE\' and purged != 1 AND user_pass=md5(md5(\'%s\')) ORDER BY user_id LIMIT 1;';

    public function __construct(ContainerInterface $container = null, $params = [])
    {
        try {
            $this->_container = $container;

            // Servicio para imprimir debugger
            $this->_debugger = $container->get('nupres.dumper.service');
            $debugger = $this->_debugger;

            // Escribiendo log en modo debugger
            $debugger::debugger(
                'CLASS INFO',
                array(
                    'CLASS'     => __CLASS__,
                    'FILE'      => __FILE__,
                    'METHOD'    => __METHOD__,
                    'LINE'      => __LINE__
                )
            );

            // Escribiendo log en modo debugger
            $debugger::debugger('PARAMETERS', $params);

            $this->_dbClient = MysqlClient::getInstance($container, $params);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function _login($params = [])
    {
        return $this->_dbClient->rawQuery(
            sprintf(
                self::LOGIN_QUERY,
                $params['username'],
                $params['username'],
                $params['password']
            )
        );
    }

    public function login($params = [])
    {
        return $this->_login($params);
    }

    protected function _isLoggedIn($userhash)
    {
        // Servicio para imprimir debugger
        $debugger = $this->_debugger;

        // Invocamos el servicio jwt para desencriptar datos
        $jwTokenService = $this->_container->get('nupres.jwt.service');

        $secretKeyConfig = $this->_container->getParameter('nupres_config.jwt');

        $userData = $jwTokenService::decode($userhash, $secretKeyConfig['secret_key']);

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

        // Escribiendo log en modo debugger
        $debugger::debugger(
            'VARS INFO',
            array(
                'userhash'          => $userhash,
                'secretKeyConfig'   => $secretKeyConfig,
                'database'          => $userData->database,
                'username'          => $userData->username
            )
        );

        /*
        @TODO @FIXME Encontre un problema con las sesiones tanto en local como en servergrove y es que los archivos de sesion se crean en el servidor pero al
        parecer no quedan con permisos de escritura lo que ocasiona que no se guarde
        la informacion de la sesion y cuando esto pasa... otras apis como por ejemplo
        isloggedin desde el controlador o desde el auth model tratan de recuperar los datos de la sesion por una key y pues no encuentran datos, toca seguir
        investigando, por el momento no voy a usar las sessions de php, solo
        nos basaremos en el token jwt que se entregue al cliente y desde el cliente
        persistir esta info que era lo que en principio queria que hiciera php, nos toca asi para poder seguir avanzando el front desde angular.
         */
        //$session = $this->_container->get('session');

        //Si la sesion existe, entonces
        /*
        if ($session->has($userData->database . '.' . $userData->username)) {
            return true;
        } else {
            $debugger::debugger('WARNING - NO SE OBTUVO LA SESSION: ' . $userData->database . '.' . $userData->username);
        }*/

        // @IMPORTANT: Si no resolvermos lo de las sessiones en php
        // lo que haremos para garantizar la autenticidad del userhash es
        // enviar el username a la base de datos para preguntar si es valido,
        // asi estariamos verificando de que el token con tien datos reales de los
        // usuarios. lo malo de hacer esto es que apis como el isloggedin o logout
        // por ejemplo estan haciendo que la base de datos reciba mas carga que la capa de
        // sesiones de php quien deberia ser el responsable

        // Por ahora vamos a hacerlos validando si existen las propiedades en el objeto del token, pero la idea es verificar que exista una session en php:
        if (property_exists($userData, 'database') &&
            property_exists($userData, 'username')) {
            return true;
        }

    }

    public function isLoggedIn($userhash)
    {
        return $this->_isLoggedIn($userhash);
    }
}
