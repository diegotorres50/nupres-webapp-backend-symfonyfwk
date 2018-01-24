<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Auth
{
    private $_dbClient;

    const LOGIN_QUERY = 'SELECT * FROM %s;';

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
        return $this->_dbClient->rawQuery(sprintf(self::LOGIN_QUERY, 'usuarios'));
    }
}
