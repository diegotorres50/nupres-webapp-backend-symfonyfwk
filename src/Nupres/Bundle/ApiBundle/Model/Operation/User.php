<?php

namespace Nupres\Bundle\ApiBundle\Model\Operation;

use Symfony\Component\DependencyInjection\ContainerInterface;

class User
{
    private $_container;
    private $_request;
    private $_dumper;
    private $_jwt;
    private $_userhash;
    private $_session;

    public function __construct(ContainerInterface $container = null, $userhash = null)
    {
        try {
            $this->_container = $container;
            $this->_request = $container->get('nupres.request.service');
            $this->_dumper = $container->get('nupres.dumper.service');
            $this->_jwt = $container->get('nupres.jwt.service');
            $this->_userhash = $userhash;
            $this->_session = $container->get('session');
        } catch (\Exception $ex) {
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

    public function getDbName()
    {
        return $this->_getDbName();
    }

    private function _getDataFromUserhash()
    {
        $secretKeyConfig = $this->_container->getParameter('nupres_config.jwt');

        $jwtService = $this->_jwt;

        return $jwtService::decode($this->_userhash, $secretKeyConfig['secret_key']);
    }

    public function getDataFromUserhash()
    {
        return $this->_getDataFromUserhash();
    }

    private function _getDataFromSession()
    {
        return $this->_session->get($this->_getDbName() . '.' . $this->_getUsername());
    }

    public function getDataFromSession()
    {
        return $this->_getDataFromSession();
    }
}
