<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// Para verificar si el usuario tiene session activa
use Nupres\Bundle\ApiBundle\Model\Security\Auth;

// Para hacer CRUD de la tabla pacientes
use Nupres\Bundle\ApiBundle\Model\Operation\Patient;

// Para obtener datos del objeto usuario
use Nupres\Bundle\ApiBundle\Model\Operation\User;

class PatientController extends Controller
{
    public function addAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

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

            // Obtenemos por post los parametros del body / application/x-www-form-urlencoded

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->request->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            // $id es el documento del paciente
            $id = trim($request->request->get('id', null));

            // $nombres del paciente
            $nombres = trim($request->request->get('nombres', null));

            // $apellidos del paciente
            $apellidos = trim($request->request->get('apellidos', null));

            // $genero del paciente
            $genero = trim($request->request->get('genero', null));

            // $fechaNacimiento del paciente
            $fechaNacimiento = trim($request->request->get('fecha_nacimiento', null));
            // $talla del paciente
            $talla = trim($request->request->get('talla', null));

            // $mediaEnvergadura del paciente
            $mediaEnvergadura = trim($request->request->get('media_envergadura', null));

            // $alturaRodilla del paciente
            $alturaRodilla = trim($request->request->get('altura_rodilla', null));

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->request->all();

            // Validamos que exista el id en el request
            if (!empty($id)) {
                $params['id'] = $id;
            } else {
                throw new \Exception("id no fue encontrado");
            }

            // Validamos que exista nombres en el request
            if (!empty($nombres)) {
                $params['nombres'] = $nombres;
            } else {
                throw new \Exception("nombres no fue encontrado");
            }

            // Validamos que exista apellidos en el request
            if (!empty($apellidos)) {
                $params['apellidos'] = $apellidos;
            } else {
                throw new \Exception("apellidos no fue encontrado");
            }

            // Validamos que exista genero en el request
            if (!empty($genero)) {
                $params['genero'] = $genero;
            } else {
                throw new \Exception("genero no fue encontrado");
            }

            // Validamos que exista fechaNacimiento en el request
            if (!empty($fechaNacimiento)) {
                $params['fecha_nacimiento'] = $fechaNacimiento;
            } else {
                throw new \Exception("fecha_nacimiento no fue encontrado");
            }

            // Validamos que exista talla en el request
            if (!empty($talla)) {
                $params['talla'] = $talla;
            } else {
                throw new \Exception("talla no fue encontrado");
            }

            // Validamos que exista mediaEnvergadura en el request
            if (!empty($mediaEnvergadura)) {
                $params['media_envergadura'] = $mediaEnvergadura;
            }

            // Validamos que exista alturaRodilla en el request
            if (!empty($alturaRodilla)) {
                $params['altura_rodilla'] = $alturaRodilla;
            }

            $params['created_at'] = date('Y-m-d H:m:s');

            $params['created_by'] = $userService->getUsername();

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS:', $params);

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->add($params);

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = intval($data);

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

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            // $id es el documento del paciente
            $params['fields'] = trim($request->query->get('fields', '*'));

            // $id es el documento del paciente
            $params['offset'] = trim($request->query->get('offset', 0));

            // $nombres del paciente
            $params['count'] = trim($request->query->get('count', 1));

            // $apellidos del paciente
            $params['order_by_column'] = trim($request->query->get('order_by_column', 1));

            // $apellidos del paciente
            $params['order_by_sort'] = trim($request->query->get('order_by_sort', 'ASC'));

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', $params);

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->query->all();

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->getAll($params);

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

    public function purgeAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API purgeAction');

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

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', $params);

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->query->all();

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->deleteAll();

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = intval($data);

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

    public function deleteAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API deleteAction');

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

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $id = trim($request->query->get('id', null));

            // Validamos que exista el userhash en el request
            if (empty($id)) {
                throw new \Exception("id no fue encontrado");
            }

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', array_merge($params, array('id' => $id)));

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->query->all();

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->updateById($id, array('purged' => 1));

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

    public function updateAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API updateAction');

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

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->request->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $id = trim($request->request->get('id', null));

            // Validamos que exista el userhash en el request
            if (empty($id)) {
                throw new \Exception("id no fue encontrado");
            }

            // $nombres
            $nombres = trim($request->request->get('nombres', null));

            // validamos
            if (!empty($nombres)) {
                $paramsToUpdate['nombres'] = $nombres;
            }

            // $apellidos
            $apellidos = trim($request->request->get('apellidos', null));

            // validamos
            if (!empty($apellidos)) {
                $paramsToUpdate['apellidos'] = $apellidos;
            }

            // $genero
            $genero = trim($request->request->get('genero', null));

            // validamos
            if (!empty($genero)) {
                $paramsToUpdate['genero'] = $genero;
            }

            // $fechaNacimiento
            $fechaNacimiento = trim($request->request->get('fechaNacimiento', null));

            // validamos
            if (!empty($fechaNacimiento)) {
                $paramsToUpdate['fechaNacimiento'] = $fechaNacimiento;
            }

            // $talla
            $talla = trim($request->request->get('talla', null));

            // validamos
            if (!empty($talla)) {
                $paramsToUpdate['talla'] = $talla;
            }

            // $mediaEnvergadura
            $mediaEnvergadura = trim($request->request->get('media_envergadura', null));

            // validamos
            if (!empty($mediaEnvergadura)) {
                $paramsToUpdate['media_envergadura'] = $mediaEnvergadura;
            }

            // $alturaRodilla
            $alturaRodilla = trim($request->request->get('altura_rodilla', null));

            // validamos
            if (!empty($alturaRodilla)) {
                $paramsToUpdate['altura_rodilla'] = $alturaRodilla;
            }

            $paramsToUpdate['modified_at'] = date('Y-m-d H:m:s');

            $paramsToUpdate['modified_by'] = $userService->getUsername();

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', array_merge($params, array('id' => $id)));

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->request->all();

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->updateById($id, $paramsToUpdate);

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

    public function basicSearchAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API searchBasicAction');

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

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            } else {
                $params['userhash'] = $userhash;
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Validamos si el usuario tiene session activa
            if (!$authService->isLoggedIn($userhash)) {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Invocamos el servicio de gestion de usuarios
            $userService = new User($this->container, $userhash);

            // Obtenemos del objeto usuario la info de la bd a la que se conectara
            $params['database'] = $userService->getDbName();

            $pattern = trim($request->query->get('pattern', null));

            // Validamos que exista genero en el request
            if (!empty($pattern)) {
                $params['pattern'] = $pattern;
            } else {
                throw new \Exception("criterio de busqueda no fue encontrado");
            }

            // $id es el documento del paciente
            $params['fields'] = trim($request->query->get('fields', '*'));

            // $id es el documento del paciente
            $params['offset'] = trim($request->query->get('offset', 0));

            // $nombres del paciente
            $params['count'] = trim($request->query->get('count', 1));

            // $apellidos del paciente
            $params['order_by_column'] = trim($request->query->get('order_by_column', 1));

            // $apellidos del paciente
            $params['order_by_sort'] = trim($request->query->get('order_by_sort', 'ASC'));

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', $params);

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->query->all();

            // Invocamos el servicio de pacientes
            $patientService = new Patient($this->container, $params);

            $data = $patientService->basicSearch($params);

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
