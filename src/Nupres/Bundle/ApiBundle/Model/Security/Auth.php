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
        investigando, por el momento no voy a usar las sessions de php.
         */

        // Invocamos el servicio de sessions que hicimos a pedal.
        $sessionService = $this->_container->get('nupres.session.service');

        $sessionService->setDbAlias($userData->database);

        //Si la sesion existe, entonces
        if ($sessionService->exists($userData->session_id)) {
            return true;
        } else {
            $debugger::debugger('WARNING - NO SE ENCONTRO REGISTRADA LA SESSION: ' . $userData->session_id);
        }
    }

    public function isLoggedIn($userhash)
    {
        return $this->_isLoggedIn($userhash);
    }

    protected function _logout($userhash)
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
        investigando, por el momento no voy a usar las sessions de php.
         */

        // Invocamos el servicio de sessions que hicimos a pedal.
        $sessionService = $this->_container->get('nupres.session.service');

        $sessionService->setId($userData->session_id);

        $sessionService->setDbAlias($userData->database);

        //Si la sesion existe, entonces
        if ($sessionService->close()) {
            return true;
        } else {
            $debugger::debugger('WARNING - NO SE PUDO BORRAR LA SESSION REGISTRADA: ' . $userData->session_id);
        }
    }

    public function logout($userhash)
    {
        return $this->_logout($userhash);
    }
}
