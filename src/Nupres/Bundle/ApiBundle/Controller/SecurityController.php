<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nupres\Bundle\ApiBundle\Model\DataBase\MysqlClient;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        // Respuesta a entregar
        $feedback = array();

        // Parametros para hacer el login
        $params = array();

        // Parametros de entrada en la api
        $entry = array();

        try {
            // Obtenemos por post los valores de conexion
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $database = $request->request->get('database');

            /*if (array_key_exists('username', $bodyParams) &&
              !empty($bodyParams['username']) &&
              !is_null($bodyParams['username'])) {
                $params['username'] = $bodyParams['username'];
            } else {
                throw new Exception("usename no esta definido");
            }
            if (array_key_exists('password', $bodyParams) &&
              !empty($bodyParams['password']) &&
              !is_null($bodyParams['password'])) {
                $params['password'] = $bodyParams['password'];
            } else {
                throw new Exception("password no esta definido");
            }*/

            //Si la sesion ya existe, no mostramos el formulario de login
            if ($request->getSession()->has($username) &&
                !empty($request->getSession()->has($username))) {
                print_r('ya tiene una session');
            } else {
                $session=$request->getSession();
                $session->set($username, "user data");
                $session->set("database", $database);
                print_r('no tenia session y se le ha creado la session: ' . $session->getName());
            }

            die;

            //$_mysqlClient = MysqlClient::getInstance(array());
            //$_results = $_mysqlClient->rawQuery('SELECT * FROM informe_cuidado_critico_general;');
            $feedback['status'] = 1;
            $feedback['code'] = 200;
            $feedback['data'] = $data;

            $response = new Response();
            $response->setContent(json_encode(array(
                'data' => $_results,
            )));
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
}
