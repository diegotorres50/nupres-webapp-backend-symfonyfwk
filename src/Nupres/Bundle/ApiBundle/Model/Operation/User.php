<?php

namespace Nupres\Bundle\ApiBundle\Model\Operation;

use Symfony\Component\DependencyInjection\ContainerInterface;

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
}
