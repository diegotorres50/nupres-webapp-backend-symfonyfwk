<?php

namespace Nupres\Bundle\ApiBundle\Model\Operation;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class Patient
{
    private $_dbClient;

    private $_container;

    private $_request;

    private $_dumper;

    private $_dbEntities;

    const GET_ALL_QUERY = 'SELECT %s FROM %s ORDER BY %s %s LIMIT %s, %s;';

    public function __construct(ContainerInterface $container = null, $params = [])
    {
        try {
            $this->_container = $container;
            $this->_dbClient = MysqlClient::getInstance($container, $params);
            $this->_request = $container->get('nupres.request.service');
            $this->_dumper = $container->get('nupres.dumper.service');
            $dbEntitiesService = $container->get('nupres.db_entities.service');
            $this->_dbEntities = $dbEntitiesService::getDbEntities();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function _add($params = [])
    {
        if (boolval($this->_request->getQueryString('debugger'))) {
            dump($params);
        }

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

        $factoriesMapService = $this->_container->get('nupres.factories_map.service');
        $factoriesMap = $factoriesMapService::getFactoriesMap();

        $database = $factoriesMap[strtoupper($params['database'])];

        if ($patient = $this->_dbClient->insert($database . '.' . $this->_dbEntities['TABLE_PACIENTES'], $data)) {
            return $patient;
        } elseif (boolval($this->_request->getQueryString('debugger'))) {
            $dumper = $this->_dumper;
            $dumper::dump($this->_dbClient->getLastQuery());
            $dumper::dump($this->_dbClient->getLastError());
        }
    }

    public function add($params = [])
    {
        return $this->_add($params);
    }

    private function _getAll($params = [])
    {
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

        foreach ($params as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $data[$key] = $value;
        }

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
