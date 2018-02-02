<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nupres\Bundle\ApiBundle\Model\Security\Auth;

// Para obtener datos del objeto usuario
use Nupres\Bundle\ApiBundle\Model\Operation\User;

class ConfigController extends Controller
{
    public function getAllAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API getAllAction');

            // Obtenemos del header la api key para validar el acceso
            $apiKey = $request->headers->get('Authorization');

            // Retornamos error de parametros si no se especifica credencial de acceso
            if (empty($apiKey)) {
                throw new \Exception("Error de credenciales");
            }

            // Invocamos el servicio que valida las credenciales de la api
            $credentialsService = $this->container->get('nupres.credentials.service');

            // Verificamos las credenciales de acceso usando la decodificacion base64
            if (!$credentialsService::checked($this->container, $apiKey)) {
                throw new \Exception("No autorizado");
            }

            // Obtenemos por post los parametros del body / application/x-www-form-urlencoded

            // Invocamos el servicio que valida las credenciales de la api
            $nupresConfigMysql = $this->container->getParameter('nupres_config.mysql');

            $nupresConfigJwt = $this->container->getParameter('nupres_config.jwt');

            $nupresConfigApiKey = $this->container->getParameter('nupres_config.api_key');

            $factoriesMapService = $this->container->get('nupres.factories_map.service');
            $factoriesMap = $factoriesMapService::getFactoriesMap();

            $dbEntitiesService = $this->container->get('nupres.db_entities.service');
            $dbEntities = $dbEntitiesService::getDbEntities();

            $data = array(
                'nupres_config' => array(
                    'mysql' => $nupresConfigMysql,
                    'jwt' => $nupresConfigJwt,
                    'api_key' => $nupresConfigApiKey
                ),
                'parameters_yml' => array(
                    'database_host'     => $this->container->getParameter('database_host'),
                    'database_user'     => $this->container->getParameter('database_user'),
                    'database_password' => $this->container->getParameter('database_password')
                ),
                'factories_map' => $factoriesMap,
                'db_entities'   => $dbEntities
            );

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = $data;

            // Retornamos un http response
            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            // Para los errores controlados, cosntruimos la respuesta
            $feedback['status'] = 0;
            $feedback['msg'] = 'Error';
            $feedback['code'] = 400;
            $feedback['data'] = null;
            $feedback['error'] = array();
            $feedback['error']['code'] = $e->getCode();
            $feedback['error']['message'] = $e->getMessage();
            $feedback['error']['line'] = $e->getLine();
            $feedback['error']['file'] = $e->getFile();
            $feedback['error']['method'] = __METHOD__;
            $feedback['error']['trace'] = $e->__toString();

            // Respondemos un error controlado
            return new Response(
                json_encode($feedback),
                200,
                array(
                    'Content-Type' => 'application/json'
                )
            );
        }
    }
}
