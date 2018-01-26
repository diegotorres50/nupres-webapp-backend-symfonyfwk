<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $_mysqlClient = MysqlClient::getInstance($this->container, array());
        $_results = $_mysqlClient->rawQuery('SELECT * FROM informe_cuidado_critico_general;');

        $response = new Response();
        $response->setContent(json_encode(array(
            'data' => $_results,
        )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
