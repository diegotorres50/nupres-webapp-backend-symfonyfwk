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

        try {
            // Obtenemos por post los valores de conexion
            $username = trim($request->request->get('username', null));
            $password = trim($request->request->get('password', null));
            $database = trim($request->request->get('database', null));

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
                throw new \Exception("database no fue encontrado");
            }

            /*
            // Obtenemos del contenedor de servicios el mapeo de secciones etce vs feeds de bbc news
            $feedsAliasService = $this->container->get('etce_bbcnews.feeds_alias');

            // Retorna la lista de secciones como key y alias de feeds como valor
            $feedsAlias = array (
                'bundle_entity_sections_feeds' => $feedsAliasService::getFeedsMap(),
                'xalok_config_sections_feeds' => $this->container->getParameter('bbc_news_feeds_vs_etce_sections'),
                'xalok_config_gallery_feed_section_default' => $this->container->getParameter('bbc_news_gallery_feed_section_default'),
                'xalok_config_page_article_class' => $this->container->getParameter('bbc_news_page_article_class')
            );
             */

            // retrieve GET and POST variables respectively
            /*
            $feed = $request->query->get('feed', 'mundo-internacional');
            $section = $request->query->get('section', 'internacional');

            // Levantamos el servicio de importacion para acceder a un metodo especifico
            // que nos devuelve el json del feed
            $importerService = new Importer(
                $this->container,
                $this->get('logger'),
                $this->get('wfcms_xalok.importer_service')
            );*/


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

            //die;

            //$_mysqlClient = MysqlClient::getInstance(array());
            //$_results = $_mysqlClient->rawQuery('SELECT * FROM informe_cuidado_critico_general;');
            //$feedback['entry'] = $request->query->all();
            $feedback['status'] = 1;
            $feedback['code'] = 200;
            $feedback['data'] = 'TODOS LOS DATOS';

            $response = new Response();
            $response->setContent(json_encode($feedback));
            $response->headers->set('Content-Type', 'application/json');
            /*
            $response = new Response(json_encode($result));

            $response->headers->set('Content-Type', 'application/json');
            */


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
