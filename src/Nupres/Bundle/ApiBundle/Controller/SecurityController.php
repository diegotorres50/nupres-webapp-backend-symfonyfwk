<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nupres\Bundle\ApiBundle\Model\Security\Auth;

use Nupres\Bundle\ApiBundle\Entity\Factories;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        // Respuesta a entregar
        $feedback = array();

        // Parametros para hacer el login
        $params = array();

        try {
            // Obtenemos por post los valores de conexion
            $username = trim($request->request->get('username', null));
            $password = trim($request->request->get('password', null));
            $database = trim($request->request->get('factory', null));

            // Construimos parcialmente la respuesta
            $feedback['entry'] = $request->request->all();

            // Validamos datos de entrada
            if (!empty($username)) {
                $params['username'] = $username;
            } else {
                throw new \Exception("username no fue encontrado");
            }

            if (!empty($password)) {
                $params['password'] = $password;
            } else {
                throw new \Exception("clave no fue encontrada");
            }

            if (!empty($database)) {
                $params['database'] = $database;
            } else {
                throw new \Exception("factory no fue encontrado");
            }

            $authService = new Auth($this->container, $params);
            $userData = $authService->login($params);

            if (is_array($userData) and !empty($userData)) {
                $userData = $userData[0];
                $jwTokenService = $this->container->get('nupres.jwt.service');
                $data = $jwTokenService::encode(
                    array_merge(
                        $userData,
                        array('time' => time())
                    )
                );
                $userHash = $jwTokenService::encode(
                    array(
                        'database' => $database,
                        'username' => $username,
                        'time' => time()
                    )
                );

                //Si la sesion ya existe, no mostramos el formulario de login
                if ($request->getSession()->has($database . '.' . $username) &&
                    !empty($request->getSession()->has($database . '.' . $username))) {
                    //La session ya existia
                } else {
                    // Creamos la session
                    $session=$request->getSession();
                    $session->set($database . '.' . $username, $userData);
                    $session->set("database", $database);
                }
            } else {
                throw new \Exception("usuario o clave invalida");
            }

            $feedback['status'] = 1;
            $feedback['code'] = 200;
            $feedback['data']['jwt'] = $data;
            $feedback['data']['user_hash'] = $userHash;
            $feedback['data']['session'] = array(
                'id' => $request->getSession()->getId(),
                'name' => $request->getSession()->getName()
            );

            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            $feedback['status'] = 0;
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
        $session = $request->getSession();

        //Si la sesion existe, entonces si la limpiamos
        if ($session->has("username")) {
            //Cerramos ahora la sesion en el navegador
            $session->clear();
            print_r('se borro la session');
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'data' => 'OK',
        )));
        $response->headers->set('Content-Type', 'application/json');

        die;


        return $response;
    }

    public function isloggedinAction(Request $request)
    {
        $username = $request->query->get('username');
        $session = $request->getSession();

        //Si la sesion existe, entonces si la limpiamos
        if ($session->has($username)) {
            print_r($session->all());
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'data' => 'OK',
        )));
        $response->headers->set('Content-Type', 'application/json');

        die;


        return $response;
    }

    public function jwtDecodeAction(Request $request)
    {
        // Respuesta a entregar
        $feedback = array();

        // Parametros para hacer el login
        $params = array();

        try {
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
            $feedback['code'] = 200;
            $feedback['data'] = $data;

            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            $feedback['status'] = 0;
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
