<?php

namespace Nupres\Bundle\ApiBundle\Model\DataBase;

use \MysqliDb;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MysqlClient
{

    private $_client = null;

    private static $_instance; // The singleton instance

    private $_dbHost;

    private $_dbUser;

    private $_dbPass;

    private $_dbName;

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
            $this->_dbHost = $container->getParameter('database_host');
            $this->_dbUser = $container->getParameter('database_user');
            $this->_dbPass = $container->getParameter('database_password');

            $factoriesMapService = $container->get('nupres.factories_map.service');
            $factoriesMap = $factoriesMapService::getFactoriesMap();

            // Como persistir esto que no sea session
            $this->_dbName = $factoriesMap[strtoupper($arguments['database'])];

            $this->_client = new MysqliDb(
                $this->_dbHost,
                $this->_dbUser,
                $this->_dbPass,
                $this->_dbName
            );
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
