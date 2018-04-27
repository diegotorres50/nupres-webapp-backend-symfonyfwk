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
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API loginAction');

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

            // Escribiendo log en modo debugger
            $debugger::debugger('API PARAMS: ', $params);

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

                /*
                @TODO @FIXME Encontre un problema con las sesiones tanto en local como en servergrove y es que los archivos de sesion se crean en el servidor pero al
                parecer no quedan con permisos de escritura lo que ocasiona que no se guarde
                la informacion de la sesion y cuando esto pasa... otras apis como por ejemplo
                isloggedin desde el controlador o desde el auth model tratan de recuperar los datos de la sesion por una key y pues no encuentran datos, toca seguir
                investigando, por el momento no voy a usar las sessions de php

                Usare un modelo session custom que gestione la info de la sesion del usuario.
                 */

                // Invocamos el servicio de sessions que hicimos a pedal.
                $sessionService = $this->container->get('nupres.session.service');

                // Preparamos la data que queremos guardar al crear la session
                $params['data'] = array(
                                    'extra' => $userData
                                );

                // Tratamos de crear la session, si ya existe una session, este metodo
                // borra esa session y crea una nueva.
                // el create necesita como minimo el dato del nombre de la base de datos (alias)
                // para que el servicio levante las conexiones a mysql para persistir
                // las sessiones.
                //
                // @TODO este create debe estar dentro del login del modelo auth y que ahi se encargue de todo esta parte
                $sessionService->setDbAlias($database); // Las sessiones pertenecen a una bd
                $sessionService->create($username, $params['data']);

                // Creamos un userhash encriptado para re usarlo en todas las apis que requieran autenticar el usuario para verificar si tiene session activa
                $userHash = $jwTokenService::encode(
                    array(
                        'session_id'    => $sessionService->getId(),
                        'database'      => $database,
                        'username'      => $username,
                        'time'          => time(),
                        'extra'         => $userData
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

            // Retornamos un http response
            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

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

    public function logoutAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API logoutAction');

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

            /*
             @TODO @FIXME Encontre un problema con las sesiones tanto en local como en servergrove y es que los archivos de sesion se crean en el servidor pero al
             parecer no quedan con permisos de escritura lo que ocasiona que no se guarde
             la informacion de la sesion y cuando esto pasa... otras apis como por ejemplo
             isloggedin desde el controlador o desde el auth model tratan de recuperar los datos de la sesion por una key y pues no encuentran datos, toca seguir
             investigando, por el momento no voy a usar las sessions de php, solo
             nos basaremos en el token jwt que se entregue al cliente y desde el cliente
             persistir esta info que era lo que en principio queria que hiciera php, nos toca asi para poder seguir avanzando el front desde angular.
            */
            // Validamos si el usuario tiene session activa
            if ($authService->isLoggedIn($userhash)) {
                //Cerramos ahora la sesion registrada
                $authService->logout($userhash);
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

    public function isloggedinAction(Request $request)
    {
        // $feedback para construir la respuesta de la api
        $feedback = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API isloggedinAction');

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

                /*
                 @TODO @FIXME Encontre un problema con las sesiones tanto en local como en servergrove y es que los archivos de sesion se crean en el servidor pero al
                 parecer no quedan con permisos de escritura lo que ocasiona que no se guarde
                 la informacion de la sesion y cuando esto pasa... otras apis como por ejemplo
                 isloggedin desde el controlador o desde el auth model tratan de recuperar los datos de la sesion por una key y pues no encuentran datos, toca seguir
                 investigando, por el momento no voy a usar las sessions de php.
                */

                // Obtenemos del objeto usuario la info que tenemos en sesion
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
                throw new \Exception("El usuario no esta autenticado");
            }

            // Terminamos de construir la respuesta de la api
            $feedback['status'] = 1;
            $feedback['msg'] = 'Okey';
            $feedback['code'] = 200;
            $feedback['data'] = $data; // Por ahora retornamos el mismo hash

            // Retornamos un http response
            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

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

    public function jwtDecodeAction(Request $request)
    {
        // Respuesta a entregar
        $feedback = array();

        try {
            // Servicio para imprimir debugger
            $debugger = $this->container->get('nupres.dumper.service');

            // Escribiendo log en modo debugger
            $debugger::debugger('INICIO API jwtDecodeAction');

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

            // Necesario para que desde angular evite el cross domain: https://ourcodeworld.com/articles/read/291/how-to-solve-the-client-side-access-control-allow-origin-request-error-with-your-own-symfony-3-api

            // Allow all websites
            $response->headers->set('Access-Control-Allow-Origin', '*');

            $response->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept, authorization, cache-control, content-type');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');

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
                    'Content-Type'                  => 'application/json',
                    'Access-Control-Allow-Origin'   => '*',
                    'Access-Control-Allow-Headers'  => 'origin, content-type, accept, authorization, cache-control, content-type, credential',
                    'Access-Control-Allow-Methods'  => 'POST, GET, PUT, DELETE, PATCH, OPTIONS'
                )
            );
        }
    }
}
