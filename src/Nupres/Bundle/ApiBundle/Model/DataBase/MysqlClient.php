<?php

namespace Nupres\Bundle\ApiBundle\Model\DataBase;

use \MysqliDb;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MysqlClient
{

    private $_client = null;

    private static $_instance; // The singleton instance

    /*
    Get an instance of the Mysql
    @return Instance
    */
    public static function getInstance(ContainerInterface $container = null, $arguments = [])
    {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self($container, $arguments);
        }

        return self::$_instance;
    }

    public function __construct(ContainerInterface $container = null, $arguments = [])
    {
        try {
            $this->_client = new MysqliDb($container->getParameter('database_host'), $container->getParameter('database_user'), $container->getParameter('database_password'), 'nupres_dev_demo01');
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // Magic method clone is empty to prevent duplication of client
    private function __clone()
    {
    }

    // Get mysql client
    public function getClient()
    {
        return $this->_client;
    }

    // Exec raw query
    public function rawQuery($query)
    {
        return $this->_client->rawQuery($query);
    }
}
