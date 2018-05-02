<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// Para verificar si el usuario tiene session activa
use Nupres\Bundle\ApiBundle\Model\Security\Auth;

// Para obtener datos del objeto usuario y gestionar el CRUD
use Nupres\Bundle\ApiBundle\Model\Operation\User;

// Conjunto de funciones generales de validacion
use Nupres\Bundle\ApiBundle\Traits\User\Common;

class UserController extends Controller
{
    // Invocamos el uso de traits para hacer el codigo mas pequenio.
    use Common;

    public function addAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API addAction');

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

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->request->all();

            // Obtenemos por post los parametros del body / application/x-www-form-urlencoded

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->request->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, ['userhash' => $userhash]);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Obtenemos del body el campo data con un json esperado como valor
            $data = trim($request->request->get('data', null));

            // Si no es un json invalidamos la peticion
            if (!$data = json_decode($data, true)) {
                throw new \Exception("Parametro data no encontrado");
            }

            // Una vez decodificado el json, se lo pasamos al trait
            // para verificar si este contiene los atributos esperados
            // para crear el registro en la base de datos
            if (!$this->isValidRecord($data)) {
                throw new \Exception("Parametros del data no validos");
            }

            // Escribiendo log en modo debugger
            $debugger::debugger('DATA PARAM:', $data);

            // Invocamos el servicio de gestion de usuarios
            // para insertar el registro
            $userService = new User($this->container, $userhash);

            // Insertamos el registro mediante el modelo
            $userData = $userService->add($data);

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = intval($userData);
            $feedback['msg'] = 'Okey'; // Asi el registro ya existe deberiamos saltar la excepcion, toca crear un metodo if exists user
            $feedback['code'] = 200;

            // Retornamos un http response
            $response = new Response();
            $response->setContent(json_encode($feedback));

            // Necesario para que desde angular evite el cross domain: https://ourcodeworld.com/articles/read/291/how-to-solve-the-client-side-access-control-allow-origin-request-error-with-your-own-symfony-3-api

            // Allow all websites
            $response->headers->set('Access-Control-Allow-Origin', '*');

            $response->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept, authorization, cache-control, content-type');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');

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
            // Necesario para que desde angular evite el cross domain: https://ourcodeworld.com/articles/read/291/how-to-solve-the-client-side-access-control-allow-origin-request-error-with-your-own-symfony-3-api

            // Allow all websites

            return new Response(
                json_encode($feedback),
                200,
                array(
                    'Content-Type'                  => 'application/json',
                    'Access-Control-Allow-Origin'   => '*',
                    'Access-Control-Allow-Headers'  => 'origin, content-type, accept, authorization, cache-control, content-type, credential',
                    'Access-Control-Allow-Methods'  => 'POST, GET, PUT, DELETE, PATCH, OPTIONS'
                )
            );
        }
    }
}
