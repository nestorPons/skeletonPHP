<?php

namespace controllers;

use \core\{Controller};

use function PHPSTORM_META\map;

/**
 * Controlador template
 */
class ViewsTemplate
{
    const 
        FOLDERS_VIEW = \FOLDER\PUBLIC_FOLDER,
        MAIN_PAGE = \FOLDER\PUBLIC_FOLDER . \CONFIG['main'];
    private $page, $data ;


    function __construct($page, $data = null)
    {
        $this->page = $page ?? \CONFIG['main'];
        $this->data = new \core\Data($data);
        $this->check_page($page);

        //$this->data->addItem('123','value');
        $is_empty = $this->data->isEmpty();

        // Comprobamos si envia datos 
        if (!$is_empty) {
            prs($this->data);
        }
    }
    /**
     * Comprueva la existencia de la vista
     * Carga la variable privada $page añadiendo la extensión
     */
    function check_page(): bool
    {
        $result = true;
        $file =  self::FOLDERS_VIEW . $this->page . '.phtml';

        if (!file_exists($file)) {
            $this->page = '404';
            $result = False;
        }
        $this->page = $this->page . '.phtml';
        return $result;
    }
    /**
     * 
     */
    function print_view(): void
    {
        // Preprocesar la vista con los datos que se le mandand
        $arr_data = (is_object($this->data)) ? $this->data->toArray() : $this->data;
        include (self::FOLDERS_VIEW . $this->page);
    }
}
