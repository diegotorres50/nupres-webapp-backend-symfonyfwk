<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Auth
{
    private $_dbClient;

    const LOGIN_QUERY = 'SELECT * FROM usuarios WHERE (user_id = \'%s\' or user_mail = \'%s\') AND user_status = \'ACTIVE\' and purged != 1 AND user_pass=md5(md5(\'%s\')) ORDER BY user_id LIMIT 1;';



    public function __construct(ContainerInterface $container = null, $params = [])
    {
        try {
            $this->_dbClient = MysqlClient::getInstance($container, $params);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function login($params = [])
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
}
