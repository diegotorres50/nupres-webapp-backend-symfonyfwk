<?php

namespace Nupres\Bundle\ApiBundle\Model\Operation;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Patient
{
    private $_dbClient;

    private $_container;

    private $_dbEntities;

    private $_debugger;

    const GET_ALL_QUERY = 'SELECT %s FROM %s ORDER BY %s %s LIMIT %s, %s;';

    public function __construct(ContainerInterface $container = null, $params = [])
    {
        try {
            $this->_container = $container;
            $this->_dbClient = MysqlClient::getInstance($container, $params);
            $dbEntitiesService = $container->get('nupres.db_entities.service');
            $this->_dbEntities = $dbEntitiesService::getDbEntities();
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
        } catch (\Exception $ex) {
            // Escribiendo log en modo debugger
            $debugger::debugger(
                'EXCEPTION INFO',
                array(
                    'CLASS'     => __CLASS__,
                    'METHOD'    => __METHOD__,
                    'ERROR'     => array(
                        'CODE'   => $ex->getCode(),
                        'MSG'    => $ex->getMessage(),
                        'LINE'   => $ex->getLine(),
                        'FILE'   => $ex->getFile(),
                        'TRACE'  => $ex->getMessage(),
                        'MSG'    => $ex->__toString()
                        )
                )
            );

            throw $ex;
        }
    }

    private function _add($params = [])
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
        $debugger::debugger('PARAMETERS', $params);

        if (empty($params['id'])) {
            $params['id'] = 'NULL';
        }

        if (empty($params['nombres'])) {
            $params['nombres'] = 'NULL';
        }

        if (empty($params['apellidos'])) {
            $params['apellidos'] = 'NULL';
        }

        if (empty($params['genero'])) {
            $params['genero'] = 'NULL';
        }

        if (empty($params['fecha_nacimiento'])) {
            $params['fecha_nacimiento'] = 'NULL';
        }

        if (empty($params['talla'])) {
            $params['talla'] = 'NULL';
        }

        if (empty($params['media_envergadura'])) {
            $params['media_envergadura'] = 'NULL';
        }

        if (empty($params['altura_rodilla'])) {
            $params['altura_rodilla'] = 'NULL';
        }

        $data = array(
            'id'                => $params['id'],
            'nombres'           => $params['nombres'],
            'apellidos'         => $params['apellidos'],
            'fecha_nacimiento'  => $params['fecha_nacimiento'],
            'talla'             => $params['talla'],
            'genero'            => $params['genero'],
            'media_envergadura' => $params['media_envergadura'],
            'altura_rodilla'    => $params['altura_rodilla'],
        );

        // Escribiendo log en modo debugger
        $debugger::debugger('DATA', $data);

        $factoriesMapService = $this->_container->get('nupres.factories_map.service');
        $factoriesMap = $factoriesMapService::getFactoriesMap();

        $database = $factoriesMap[strtoupper($params['database'])];

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        if ($patient = $this->_dbClient->insert($database . '.' . $this->_dbEntities['TABLE_PACIENTES'], $data)) {
            return $patient;
        }
    }

    public function add($params = [])
    {
        return $this->_add($params);
    }

    private function _getAll($params = [])
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
        $debugger::debugger('PARAMETERS', $params);

        return $this->_dbClient->rawQuery(
            sprintf(
                self::GET_ALL_QUERY,
                $params['fields'],
                $this->_dbEntities['VIEW_PACIENTES_ACTIVOS'],
                $params['order_by_column'],
                $params['order_by_sort'],
                $params['offset'],
                $params['count']
            )
        );
    }

    public function getAll($params = [])
    {
        return $this->_getAll($params);
    }

    private function _deleteAll()
    {
        $this->_dbClient->where('purged', 1);
        return $this->_dbClient->delete($this->_dbEntities['TABLE_PACIENTES']);
    }

    public function deleteAll()
    {
        return $this->_deleteAll();
    }

    private function _updateById($id, $params = [])
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
            'VARS INFO',
            array(
                'id'       => $id,
                'params'   => $params
            )
        );

        foreach ($params as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $data[$key] = $value;
        }

        // Escribiendo log en modo debugger
        $debugger::debugger('DATA', $data);

        $this->_dbClient->where('id', $id);

        return array(
            'status' => intval($this->_dbClient->update($this->_dbEntities['TABLE_PACIENTES'], $data)),
            'msg' => intval($this->_dbClient->getAffectedRowsCount()) . ' records were updated'
        );
    }

    public function updateById($id, $params = [])
    {
        return $this->_updateById($id, $params);
    }
}
