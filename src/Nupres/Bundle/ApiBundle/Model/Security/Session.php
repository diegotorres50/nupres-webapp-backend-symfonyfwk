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

    private $_sessionId;

    private $_dbAlias;

    const FIND_SESSION_QUERY = 'SELECT sess_data FROM sessions WHERE (sess_id = \'%s\') ORDER BY sess_id LIMIT 1;';

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

    private function _create($username, $data = [])
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

        $database = $this->_getDbName();

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        // Para completar la darta que quetemos guardar en la sesion
        $_data = array (
            'session_id'    => md5($username),
            'database'      => $this->_getDbAlias(),
            'username'      => $username,
            'time'          => time()
        );

        $sessData = array(
            'sess_id'       => md5($username),
            'sess_data'     => json_encode(array_merge($data, $_data)),
            'sess_since'    => date("Y-m-d H:i:s")
        );

        // Guardamos en memoria el id de la session
        $this->_setId($sessData['sess_id']);

        // Escribiendo log en modo debugger
        $debugger::debugger('SESS_DATA', $sessData);

        $this->_dbClient = MysqlClient::getInstance($this->_container, ['database' => $database]);

        //Si la session ya existe la borramos
        $this->_delete();

        if ($session = $this->_dbClient->insert($database . '.' . $this->_dbEntities['TABLE_SESSIONS'], $sessData)) {
            // Retornamos true o false
            return $session;
        }
    }

    public function create($username, $data = [])
    {
        return $this->_create($username, $data);
    }

    private function _setId($sessionId)
    {
        $this->_sessionId = $sessionId;
    }

    public function setId($sessionId)
    {
        $this->_setId($sessionId);
    }

    private function _setDbAlias($alias)
    {
        $this->_dbAlias = $alias;
    }

    private function _getDbAlias()
    {
        return $this->_dbAlias;
    }

    private function _getDbName()
    {
        $factoriesMapService = $this->_container->get('nupres.factories_map.service');
        $factoriesMap = $factoriesMapService::getFactoriesMap();

        return $factoriesMap[strtoupper($this->_getDbAlias())];
    }

    public function setDbAlias($alias)
    {
        $this->_setDbAlias($alias);
    }

    private function _getId()
    {
        return $this->_sessionId;
    }

    public function getId()
    {
        return $this->_getId();
    }

    private function _delete()
    {
        $this->_dbClient = MysqlClient::getInstance($this->_container, ['database' => $this->_getDbName()]);
        // Funciona despues de usar create() porque alli se guarda el cliente de la bd
        $this->_dbClient->where('sess_id', $this->_getId());
        return $this->_dbClient->delete($this->_dbEntities['TABLE_SESSIONS']);
    }

    private function _exists($sessionId)
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


        $database = $this->_getDbName();

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        try {
            $this->_dbClient = MysqlClient::getInstance($this->_container, ['database' => $database]);
        } catch (\Exception $ex) {
            // Hagamos debugger aqui
            return false;
        }

        // Retornamos un boolean
        return boolval($this->_dbClient->rawQuery(
            sprintf(
                self::FIND_SESSION_QUERY,
                $sessionId
            )
        ));
    }

    public function exists($sessionId)
    {
        return $this->_exists($sessionId);
    }

    public function close()
    {
        return $this->_delete();
    }

    private function _getData($sessionId)
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

        $database = $this->_getDbName();

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        try {
            $this->_dbClient = MysqlClient::getInstance($this->_container, ['database' => $database]);
        } catch (\Exception $ex) {
            // Hagamos debugger aqui
            return false;
        }

        // Retornamos los datos encontrados
        return json_decode($this->_dbClient->rawQuery(
            sprintf(
                self::FIND_SESSION_QUERY,
                $sessionId
            )
        )['0']['sess_data'], true);
    }

    public function get($sessionId)
    {
        return $this->_getData($sessionId);
    }
}
