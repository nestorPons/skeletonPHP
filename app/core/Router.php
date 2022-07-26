<?php

namespace core;

/***  
 * Clase controlador principal.
 * Routea la url a otros controladores secundarios si existieran y 
 * si no el mismo controlara el enrutamiento. 
 */
/**
 * ej: 
 * http://localhost/company/this->controller/this->action
 * 
 * Comprueba y enruta la peticuiOn por url
 */

class Router
{
    // Vista de inicio
    const 
        MAIN =  \CONFIG['main'],
        PUBLIC_FOLDER = \FOLDER\PUBLIC_FOLDER;

    private
        $view,
        $data,
        $db,
        $controller,
        $action;

    function __construct($params)
    {   
        $this->data = new Data;
        if (isset($params['data'])) $this->data->addItems($params['data']);
        $this->view = $params['view'] ?? self::MAIN;

        // Valores por defecto
        $this->db = \CONFIG['db'];
        $this->controller =  ucfirst($params['controller'] ?? '');

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') $this->isPost($params);
        elseif (strtoupper($_SERVER['REQUEST_METHOD']) === 'GET')  $this->isGet();
    }
    private function isGet()
    {

        // Comprobar si la vista existe 
        $cls_view = new \controllers\ViewsTemplate(self::PUBLIC_FOLDER . $this->view, $this->data);

        return $cls_view->print_view();

        // Comprobar si tiene un controlador

        /* if (empty($this->db)) {
            // Si no encontramos la base datos vamos a la pagina principal
            if (empty($this->controller)) $this->controller = 'main';
            $this->controller = $this->controller;
        } else {
            // Si esta vacio controlador nos envia al login
            if (empty($this->controller)) {
                $this->action = 'view';
                $this->controller = 'login';
            }
        }
        exit($this->loadController($this->controller)); */
    }
    /**
     * Petición al servidor mediante el método post 
     * Se utiliza para la creación de nuevos registros o editarlos si estos ya existen 
     * @param string $params JSON 
     *  { 
     *      controller : controlador al que se dirige
     *      data : objeto js con los datos a tratar
     *  }
     */
    private function isPost(string $params) : void
    {
        try {
            // Pasamos los datos de json a objeto Data
            $this->data->addItems($params['data'] ?? null);

            $respond = $this->loadController();

            // Siempre se devuelve un objeto json con un success de respuesta
            if (!(is_array($respond) && isset($respond['success'])))
                $respond = ($respond == true || $respond == 1)
                    ? ['success' => true, 'data' => $respond]
                    : ['success' => false];
            /* ((is_array($respond) && isset($respond['success']) && $respond['success'] == 0)) ? $respond :
            ['success' => 1, 'data' => $respond]); */

            // SALIDA 

           exit(json_encode($respond, true)); 
        } catch (\Exception $e) {
            exit(json_encode(['success' => 'false', 'mens' => 'error: ' . $e->menssage], true));
        }
    }
    // Comprobamos que exista la clase controladora
    private function isController(string $class): Bool
    {
        return (file_exists(\FOLDER\CONTROLLERS . $class . '.php'));
    }
    // Carga controlador
    // Si se le pasa argumentos cambia el controlador asignado
    private function loadController(string $controller = null): Controller
    {
        // Buscamos controlador
        if (!empty($controller)) $this->controller = ucwords($controller);
        // Antes de cargar el controlador se comprueba si tiene permsiso para la petición
        /*if(Security::isRestrict($this->controller)){          
            if ($token = Security::getJWT()){
                $dataToken = Security::GetData($token);
                if(!$dataToken->access) return false;
                $this->data->addItem($dataToken->id, 'idadmin');
            } else {
                // Si no tiene permiso se devuelve al login
                header("Refresh:0; url={$_SERVER['PHP_SELF']}");
                return false;
            }
        }; */
        $nameClass = '\\controllers\\' . $this->controller;

        $cont = $this->isController($this->controller)
            ? new $nameClass($this->data)
            : new \core\Controller($this->controller, $this->data);

        return $cont;
    }
}
