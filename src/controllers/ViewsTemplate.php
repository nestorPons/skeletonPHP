<?php

namespace controllers;

/**
 * Controlador general para las vistas
 */
class ViewsTemplate
{
    const FOLDERS_VIEW = \FOLDER\PUBLIC_FOLDER;
    private $page, $data, $exist;


    function __construct($view, $data = null)
    {
        // Comprobamos que la vista existe
        $this->view = $view;
        $this->data = new \core\Data($data);
        $this->exist = $this->check_page($view);
    }
    function check_page($view)
    {
        return file_exists($view);
    }
    /**
     * Imprime las vistas 
     */
    function print_view()
    {   
        // Declara las variables para las vistas
        foreach($this->data->getAll() as $key => $value){
            ${$key} = $value ;
        }
        if ($this->exist) include $this->view;
    }
}
