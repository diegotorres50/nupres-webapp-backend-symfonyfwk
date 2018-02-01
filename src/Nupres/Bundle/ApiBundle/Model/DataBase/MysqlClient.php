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

    private $_debugger;

    /*
    Get an instance of the Mysql
    @return Instance
    */
    public static function getInstance(ContainerInterface $container = null, $arguments = [])
    {
        // Servicio para imprimir debugger
        $debugger = $container->get('nupres.dumper.service');

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

        if (!self::$_instance) { // If no instance then make one
            // Escribiendo log en modo debugger
            $debugger::debugger(__METHOD__ . ' CREARA UNA NUEVA INSTANCIA.');
            self::$_instance = new self($container, $arguments);
        } else {
            // Escribiendo log en modo debugger
            $debugger::debugger(__METHOD__ . ' YA EXISTIA UNA INSTANCIA DE CONEXION-');
        }

        return self::$_instance;
    }

    public function __construct(ContainerInterface $container = null, $arguments = [])
    {
        try {
            // Servicio para imprimir debugger
            $this->_debugger = $container->get('nupres.dumper.service');
            $debugger = $this->_debugger;

            $this->_dbHost = $container->getParameter('database_host');
            $this->_dbUser = $container->getParameter('database_user');
            $this->_dbPass = $container->getParameter('database_password');

            $factoriesMapService = $container->get('nupres.factories_map.service');
            $factoriesMap = $factoriesMapService::getFactoriesMap();

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

            // Intentamos usar la info del userhash para indentificar la base de datos
            // Invocamos el servicio jwt para desencriptar datos
            if (!empty($arguments['userhash'])) {
                // Escribiendo log en modo debugger
                $debugger::debugger(__METHOD__ . ' SE USARA USERHASH');

                $jwTokenService = $container->get('nupres.jwt.service');

                $secretKeyConfig = $container->getParameter('nupres_config.jwt');

                // Escribiendo log en modo debugger
                $debugger::debugger(__METHOD__ . ' VARS INFO - secretKeyConfig: ' . $secretKeyConfig);

                $userData = $jwTokenService::decode($arguments['userhash'], $secretKeyConfig['secret_key']);

                $this->_dbName = $factoriesMap[strtoupper($userData->database)];
            } elseif (!empty($arguments['database'])) {
                // Escribiendo log en modo debugger
                $debugger::debugger(__METHOD__ . ' SE USARA DATABASE');

                $this->_dbName = $factoriesMap[strtoupper($arguments['database'])];
            }

            // Escribiendo log en modo debugger
            $debugger::debugger(
                'VARS INFO',
                array(
                    'arguments'     => $arguments,
                    '_dbHost'       => $this->_dbHost,
                    '_dbUser'       => $this->_dbUser,
                    '_dbPass'       => $this->_dbPass,
                    '_dbName'       => $this->_dbName,
                    'factoriesMap'  => $factoriesMap
                )
            );

            $this->_client = new MysqliDb(
                $this->_dbHost,
                $this->_dbUser,
                $this->_dbPass,
                $this->_dbName
            );
        } catch (\Exception $ex) {
            // Escribiendo log en modo debugger
            $debugger::debugger(
                'EXCEPTION INFO',
                array(
                    'CLASS'     => __CLASS__,
                    'METHOD'    => __METHOD__,
                    'ERROR'     => array(
                        'CODE'  => $ex->getCode(),
                        'MSG'   => $ex->getMessage(),
                        'LINE'   => $ex->getLine(),
                        'FILE'   => $ex->getFile(),
                        'TRACE'   => $ex->getMessage(),
                        'MSG'   => $ex->__toString()
                        )
                )
            );

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
        // Servicio para imprimir debugger
        $debugger = $this->_debugger;

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
        $debugger::debugger('QUERY PARAM: ' . $query);

        $data = $this->_client->rawQuery($query, $bindParams);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST QUERY: ' . $this->_client->getLastQuery());

        // Escribiendo log en modo debugger
        $debugger::debugger('AFFECTED ROWS: ' . $this->_client->count);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST ERROR: ' . $this->_client->getLastError());

        return $data;
    }

    // Exec insert query
    public function insert($table, $data)
    {
        // Servicio para imprimir debugger
        $debugger = $this->_debugger;

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
            'QUERY PARAMS',
            array(
                'table' => $table,
                'data' => $data
            )
        );

        $data = $this->_client->insert($table, $data);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST QUERY: ' . $this->_client->getLastQuery());

        // Escribiendo log en modo debugger
        $debugger::debugger('AFFECTED ROWS: ' . $this->_client->count);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST ERROR: ' . $this->_client->getLastError());

        return $data;
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
        // Servicio para imprimir debugger
        $debugger = $this->_debugger;

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
            'QUERY PARAMS',
            array(
                'tableName' => $tableName,
                'numRows'   => $numRows
            )
        );

        $data = $this->_client->delete($tableName, $numRows);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST QUERY: ' . $this->_client->getLastQuery());

        // Escribiendo log en modo debugger
        $debugger::debugger('AFFECTED ROWS: ' . $this->_client->count);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST ERROR: ' . $this->_client->getLastError());

        return $data;
    }

    public function update($tableName, $tableData, $numRows = null)
    {
        // Servicio para imprimir debugger
        $debugger = $this->_debugger;

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
            'QUERY PARAMS',
            array(
                'tableName' => $tableName,
                'tableData' => $tableData,
                'numRows'   => $numRows
            )
        );

        $data = $this->_client->update($tableName, $tableData, $numRows);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST QUERY: ' . $this->_client->getLastQuery());

        // Escribiendo log en modo debugger
        $debugger::debugger('AFFECTED ROWS: ' . $this->_client->count);

        // Escribiendo log en modo debugger
        $debugger::debugger('LAST ERROR: ' . $this->_client->getLastError());

        return $data;
    }

    public function getAffectedRowsCount()
    {
        return $this->_client->count;
    }
}
