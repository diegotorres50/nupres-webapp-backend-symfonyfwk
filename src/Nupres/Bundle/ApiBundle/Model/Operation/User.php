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

    public function __construct(ContainerInterface $container = null, $userhash = null)
    {
        try {
            $this->_container = $container;
            $this->_request = $container->get('nupres.request.service');
            $this->_dumper = $container->get('nupres.dumper.service');
            $this->_jwt = $container->get('nupres.jwt.service');
            $this->_userhash = $userhash;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function _getDbName()
    {
        return $this->getDataFromUserhash()->database;
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
}
