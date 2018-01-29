<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Auth
{
    private $_dbClient;

    private $_container;

    const LOGIN_QUERY = 'SELECT * FROM usuarios WHERE (user_id = \'%s\' or user_mail = \'%s\') AND user_status = \'ACTIVE\' and purged != 1 AND user_pass=md5(md5(\'%s\')) ORDER BY user_id LIMIT 1;';



    public function __construct(ContainerInterface $container = null, $params = [])
    {
        try {
            $this->_container = $container;
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
        // Invocamos el servicio jwt para desencriptar datos
        $jwTokenService = $this->_container->get('nupres.jwt.service');

        $secretKeyConfig = $this->_container->getParameter('nupres_config.jwt');

        $session = $this->_container->get('session');

        $userData = $jwTokenService::decode($userhash, $secretKeyConfig['secret_key']);
        //Si la sesion existe, entonces
        if ($session->has($userData->database . '.' . $userData->username)) {
            return true;
        }
    }

    public function isLoggedIn($userhash)
    {
        return $this->_isLoggedIn($userhash);
    }
}
