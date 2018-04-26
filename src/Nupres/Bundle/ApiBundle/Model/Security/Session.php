<?php

namespace Nupres\Bundle\ApiBundle\Model\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Session
{
    private $_dbClient;

    private $_container;

    private $_debugger;

    private $_dbEntities;

    const CREATE_SESSION_QUERY = 'SELECT * FROM usuarios WHERE (user_id = \'%s\' or user_mail = \'%s\') AND user_status = \'ACTIVE\' and purged != 1 AND user_pass=md5(md5(\'%s\')) ORDER BY user_id LIMIT 1;';

    public function __construct(ContainerInterface $container = null)
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

            $dbEntitiesService = $container->get('nupres.db_entities.service');
            $this->_dbEntities = $dbEntitiesService::getDbEntities();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function _create($params = [])
    {
        //$params['database'] debe venir aqui sino no se puede conectar el servicio que estamos creando

        /*
        CREATE TABLE `sessions` (
          `sess_id` varchar(128) COLLATE utf8_bin NOT NULL,
          `sess_data` blob NOT NULL,
          `sess_time` int(10) unsigned NOT NULL,
          `sess_lifetime` mediumint(9) NOT NULL,
          `sess_status` bit(1) NOT NULL DEFAULT b'1',
          `sess_since` datetime NOT NULL,
          PRIMARY KEY (`sess_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
         */

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
        $debugger::debugger('PARAMETERS', $params);

        $data = array(
            'sess_id'       => md5($params['username']),
            'sess_data'     => json_encode($params['data']),
            'sess_since'    => date("Y-m-d H:i:s")
        );

        // Escribiendo log en modo debugger
        $debugger::debugger('DATA', $data);

        $factoriesMapService = $this->_container->get('nupres.factories_map.service');
        $factoriesMap = $factoriesMapService::getFactoriesMap();

        $database = $factoriesMap[strtoupper($params['database'])];

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        $this->_dbClient = MysqlClient::getInstance($this->_container, $params);

        if ($session = $this->_dbClient->insert($database . '.' . $this->_dbEntities['TABLE_SESSIONS'], $data)) {
            return $session;
        }
    }

    public function create($params = [])
    {
        return $this->_create($params);
    }
}
