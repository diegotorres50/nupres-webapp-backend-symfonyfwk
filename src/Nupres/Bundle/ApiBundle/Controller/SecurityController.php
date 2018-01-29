<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nupres\Bundle\ApiBundle\Model\Security\Auth;

// Para obtener datos del objeto usuario
use Nupres\Bundle\ApiBundle\Model\Operation\User;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        // $params para construir los parametros que requiere el model
        $params = array();

        try {
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

            // $username es el user_id o user_mail en base de datos
            $username = trim($request->request->get('username', null));

            // $password es la clave del usuario en base de datos
            $password = trim($request->request->get('password', null));

            // Cada cliente tendra una base de datos (factory) independiente
            $database = trim($request->request->get('factory', null));

            // Obtenemos todos los parametros recibidos por post
            $feedback['entry'] = $request->request->all();

            // Validamos que exista el username en el request
            if (!empty($username)) {
                $params['username'] = $username;
            } else {
                throw new \Exception("username no fue encontrado");
            }

            // Validamos que exista la clave en el request
            if (!empty($password)) {
                $params['password'] = $password;
            } else {
                throw new \Exception("clave no fue encontrada");
            }

            // Validamos que exista la base de datos en el request
            if (!empty($database)) {
                $params['database'] = $database;
            } else {
                throw new \Exception("factory no fue encontrado");
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, $params);

            // Tratamos de hacer login al usuario
            $userData = $authService->login($params);

            // Si el servicio nos ha devuelto datos
            if (is_array($userData) and !empty($userData)) {
                // Obtenemos el array del primer registro devuelto de la query
                $userData = $userData[0];

                // Invocamos el servicio jwt para encriptar datos
                $jwTokenService = $this->container->get('nupres.jwt.service');

                // Encriptamos la informacion del usuario
                $data = $jwTokenService::encode(
                    array_merge(
                        $userData,
                        array('time' => time()) // time() es para cambiar el token
                    )
                );

                //Si la sesion ya existe, no mostramos el formulario de login
                if ($request->getSession()->has($database . '.' . $username) &&
                    !empty($request->getSession()->has($database . '.' . $username))) {
                    // FIXME Aqui debo hacer un clear o logout de la session
                } else {
                    // Creamos la session
                    $session=$request->getSession();
                    // Creamos una llave valor para identificar la sesion de manera unica
                    // y guardamos mas data
                    $session->set(
                        $database . '.' . $username,
                        array(
                            'user_info' => $userData,
                            'database'  => $database
                        )
                    );
                }

                // Creamos un userhash encriptado para re usarlo en todas las apis que requieran autenticar el usuario para verificar si tiene session activa
                $userHash = $jwTokenService::encode(
                    array(
                        'session_id'    => $request->getSession()->getId(),
                        'database'      => $database,
                        'username'      => $username,
                        'time'          => time()
                    )
                );
            } else {
                throw new \Exception("usuario o clave invalida");
            }

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data']['hash'] = $userHash;
            $feedback['data']['info'] = $data;

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

    public function logoutAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        try {
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
            $feedback['entry'] = $request->query->all();

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, ['userhash' => $userhash]);

            // Validamos si el usuario tiene session activa
            if ($authService->isLoggedIn($userhash)) {
                //Cerramos ahora la sesion en el navegador
                $request->getSession()->clear();
            } else {
                throw new \Exception("El usuario no esta loggeado");
            }

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = 1;

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

    public function isloggedinAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        try {
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
            $feedback['entry'] = $request->query->all();

            // $userHash es el objeto encriptado del usuario cuando hizo login
            $userhash = trim($request->query->get('userhash', null));

            // Validamos que exista el userhash en el request
            if (empty($userhash)) {
                throw new \Exception("userhash no fue encontrado");
            }

            // Invocamos el servicio de autenticacion de usuarios
            $authService = new Auth($this->container, ['userhash' => $userhash]);

            // Validamos si el usuario tiene session activa
            if ($authService->isLoggedIn($userhash)) {
                // Recuperamos los datos en session.
                // @TODO si los datos de un usuario se actualizan, debemos actualizar la session
                // Invocamos el servicio de gestion de usuarios
                $userService = new User($this->container, $userhash);

                // Obtenemos del objeto usuario la info de la bd a la que se conectara
                $userData = $userService->getDataFromSession();

                // Invocamos el servicio del jwt
                $jwTokenService = $this->container->get('nupres.jwt.service');

                // Encriptamos la informacion del usuario
                $data = $jwTokenService::encode(
                    array_merge(
                        $userData,
                        array('time' => time()) // time() es para cambiar el token
                    )
                );
            } else {
                throw new \Exception("El usuario no esta loggeado");
            }

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

    public function jwtDecodeAction(Request $request)
    {
        // Respuesta a entregar
        $feedback = array();

        try {
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

            // Obtenemos por post los valores de conexion
            $token = trim($request->request->get('token', null));

            $secretKey = trim($request->request->get('secret_key', null));

            // Construimos parcialmente la respuesta
            $feedback['entry'] = $request->request->all();

            // Validamos datos de entrada
            if (empty($token)) {
                throw new \Exception("token no fue encontrado");
            }

            if (empty($secretKey)) {
                throw new \Exception("secret_key no fue encontrado");
            }

            $jwTokenService = $this->container->get('nupres.jwt.service');

            $data = $jwTokenService::decode($token, $secretKey);

            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = $data;

            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            $feedback['status'] = 0;
            $feedback['msg'] = 'Okey';
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
