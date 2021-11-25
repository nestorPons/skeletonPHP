<?php

namespace controllers;

use \core\{Controller};

use function PHPSTORM_META\map;

/**
 * Controlador template
 */
class ViewsTemplate
{
    const FOLDERS_VIEW = \FOLDER\VIEWS;
    private $page, $data;


    function __construct($page, $data = null)
    {
        // Comprobamos que la vista existe
        $this->data = new \core\Data($data);
        prs($data);
        $this->check_page($page);
       
    }
    function check_page($page){
        $file =  self::FOLDERS_VIEW . $page . '.phtml';
        if (file_exists($file)) {
            $html = $this->print_view($page);
            $this->page = $page;
            prs('ECISTE');
        } else {
            // Si no existe enviamos un error 404 
            $html = $this->print_view('404');
           
            return $html;
        }
    }
    function print_view($page)
    {
        // Preprocesar la vista con los datos que se le mandand
        $arr_data = (is_object($this->data)) ? $this->data->toArray() : $this-_>data;

        // Si tengo datos preproceso si no noç
        if($arr_data){
            echo('Contiene datos');
        } else {
            echo ('No contiene datos');
        }
        return $arr_data;

    }


}
