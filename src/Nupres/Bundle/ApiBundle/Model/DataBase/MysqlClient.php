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

            // Intentamos usar la info del userhash para indentificar la base de datos
            // Invocamos el servicio jwt para desencriptar datos
            if (!empty($arguments['userhash'])) {
                $jwTokenService = $container->get('nupres.jwt.service');

                $secretKeyConfig = $container->getParameter('nupres_config.jwt');

                $userData = $jwTokenService::decode($arguments['userhash'], $secretKeyConfig['secret_key']);

                $this->_dbName = $factoriesMap[strtoupper($userData->database)];
            } elseif (!empty($arguments['database'])) {
                $this->_dbName = $factoriesMap[strtoupper($arguments['database'])];
            }

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
    public function rawQuery($query, $bindParams)
    {
        return $this->_client->rawQuery($query, $bindParams);
    }

    // Exec insert query
    public function insert($table, $data)
    {
        return $this->_client->insert($table, $data);
    }

    // Get mysql last error
    public function getLastError()
    {
        return $this->_client->getLastError();
    }

    // Get mysql last query
    public function getLastQuery()
    {
        return $this->_client->getLastQuery();
    }

    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        return $this->_client->where($whereProp, $whereValue, $operator, $cond);
    }

    public function delete($tableName, $numRows = null)
    {
        return $this->_client->delete($tableName, $numRows);
    }

    public function update($tableName, $tableData, $numRows = null)
    {
        return $this->_client->update($tableName, $tableData, $numRows);
    }

    public function getAffectedRowsCount()
    {
        return $this->_client->count;
    }
}
