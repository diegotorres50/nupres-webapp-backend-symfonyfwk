<?php

namespace Nupres\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Nupres\Bundle\ApiBundle\Model\Security\Auth;

use Nupres\Bundle\ApiBundle\Entity\Factories;

use Nupres\Bundle\ApiBundle\Model\Security\JWToken;

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

            $jwt = JWToken::encode([
        'id' => 1,
        'name' => 'Eduardo'
    ]);

            var_dump($jwt);

            var_dump(JWToken::decode($jwt, 'Sdw1s9x8@'));

            die;

            $authService = new Auth($this->container, $params);
            $login = $authService->login($params);

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

            $feedback['status'] = 1;
            $feedback['code'] = 200;
            $feedback['data'] = $login;

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
}
