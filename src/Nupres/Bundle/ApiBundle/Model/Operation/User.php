<?php

namespace Nupres\Bundle\ApiBundle\Model\Operation;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

// Creo que sin quere esta clase hace 2 cosas, por un lado devolver la data del userhash
// y por el otro el crud, me parece que debemos desacoplar estas 2 cosas
class User
{
    private $_container;
    private $_jwt;
    private $_userhash;
    private $_session;
    private $_debugger;

    public function __construct(ContainerInterface $container = null, $userhash = null)
    {
        try {
            $this->_container = $container;
            $this->_jwt = $container->get('nupres.jwt.service');
            $this->_userhash = $userhash;
            $this->_session = $container->get('session');
            // Servicio para imprimir debugger
            $this->_debugger = $container->get('nupres.dumper.service');
            $debugger = $this->_debugger;

            // Para mapear los alias de las tablas
            $dbEntitiesService = $container->get('nupres.db_entities.service');
            $this->_dbEntities = $dbEntitiesService::getDbEntities();

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
            $debugger::debugger('USERHASH: ' . $userhash);
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

    private function _getDbAlias()
    {
        return $this->getDataFromUserhash()->database;
    }

    // Este metodo debe es retornar el nombre verdadero de bd
    // en lugar del alias de bd que se especifica en el login
    private function _getDbName()
    {
        return $this->getDataFromUserhash()->database;
    }

    private function _getUsername()
    {
        return $this->getDataFromUserhash()->username;
    }

    private function _getSessionId()
    {
        return $this->getDataFromUserhash()->session_id;
    }

    private function getSessionId()
    {
        return $this->_getSessionId();
    }

    public function getDbName()
    {
        return $this->_getDbName();
    }

    private function _getDataFromUserhash()
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

        $secretKeyConfig = $this->_container->getParameter('nupres_config.jwt');

        // Escribiendo log en modo debugger
        $debugger::debugger('secretKeyConfig: ' . $secretKeyConfig);

        $jwtService = $this->_jwt;

        return $jwtService::decode($this->_userhash, $secretKeyConfig['secret_key']);
    }

    public function getDataFromUserhash()
    {
        return $this->_getDataFromUserhash();
    }

    private function _getDataFromSession()
    {
        // Invocamos el servicio de sessions que hicimos a pedal.
        $sessionService = $this->_container->get('nupres.session.service');

        $sessionService->setDbAlias($this->_getDbName());

        //Retornamos informacion de la data en session
        return ($sessionService->get($this->_getSessionId()));
    }

    public function getDataFromSession()
    {
        return $this->_getDataFromSession();
    }

    public function getUsername()
    {
        return $this->_getUsername();
    }

    // Esto es parte del crud
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

        // Aqui el $data se prepara para enviar a la bd
        // y no validamos los datos, esta validacion lo hace la api controller
        $data = array(
            'user_id'   => $params['userid'],
            'user_name' => $params['username'],
            'user_mail' => $params['usermail'],
            'user_pass' => $params['userpass']
        );

        // Escribiendo log en modo debugger
        $debugger::debugger('DATA', $data);

        // Del factorymap sacamos el nombre real de la base de datos partiendo
        // del alias de bd especificado en el login
        $factoriesMapService = $this->_container->get('nupres.factories_map.service');
        $factoriesMap = $factoriesMapService::getFactoriesMap();

        // Nombre real de la bd (Not alias)
        $database = $factoriesMap[strtoupper($this->_getDbAlias())];

        // Escribiendo log en modo debugger
        $debugger::debugger('DATABASE: '. $database);

        // Levantamos el cliente de base de datos
        $this->_dbClient = MysqlClient::getInstance($this->_container, ['database' => $database]);

        // Insertamos el registro
        if ($user = $this->_dbClient->insert($database . '.' . $this->_dbEntities['TABLE_USUARIOS'], $data)) {
            return $user;
        }
    }

    public function add($params = [])
    {
        return $this->_add($params);
    }
}
