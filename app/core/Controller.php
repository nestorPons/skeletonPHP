<?php namespace core;

/**
 * Clase para ser expansión de otras subclases o clases dedicadas 
 * Tiene lo mínimo para la creación de una subclase: 
 *  Método para requerir una vista
 *  Método para requerir datos a los modelos (abstracto)
 *  Método para añadir/editar/borrar datos a los modelos (abstracto)
 */
class Controller{
    protected $conn, $controller, $data, 
        // Variable que indica si la zona necesita autentificación
        // Hay que sobreescribirla en los controladores que no necesiten de la restricción
        $restrict = true;

    public $result = null;

    function __construct($controller = null, $Data = null){
        // Obtenemos el controlador
        $this->controller =strtolower($controller ?? $this->getController());
        $this->Data = $Data; 

        }

    /**
     * Método genérico para guardar registros comprueba que es nuevo o edicion y envia los datos al metodo apropiado
     */
    protected function save(Data $Data){
        // Quitamos los datos inecesarios
        $Data->delete('idadmin');
        if($Data->id == -1 ) return $this->new($Data);
        else return $this->update($Data);
    }
        /**
     * Método por defecto de agregación de registros a la base de datos
     */
    protected function new(){
        return $this->exec('new', 'add');
    }
    /**
     * Método genérico para actualizar registros
     */
    protected function update(){
        return $this->exec('save', 'saveById');
    }
    /**
     * Método por defecto de consulta de datos 
     */
    protected function get(){
        return $this->exec('get', 'getById');
    }

    /**
     * Método por defecto de consulta de datos entre parametros
     */
    protected function getBetween(){
        return $this->exec('between', 'getBetween');
    }
    /**
     * Método por defecto de eliminación de registros por id
     */
    protected function del(){
        return $this->exec('del', 'deleteById');
    }
    private function getController(){
        $arr_controller= explode('\\',get_class($this));
        $controller = end($arr_controller);
        return strtolower($controller);
    }
    protected function getModel(){
        return (file_exists(\FOLDER\MODELS . ucfirst($this->controller) . '.php'))
            ? 'models\\' . ucfirst($this->controller)
            : 'core\\Query';
    }
    private function exec (String $method, String $method_generic){

        $name_model = $this->getModel(); 
        $model = ($name_model == '\core\Query')
            // Si es genearl query pasamos solo el nombre de la tabla 
            ? new $name_model($this->controller)
            // En caso contrario pasamos al constructor todo los datos 
            : new $name_model($this->Data->toArray());

        $model->id = $this->Data->id ?? null;
        // Si no llevamos datos, no pasamos el objeto data 
        // Para poder utilizar directamente con Query
        if($this->Data->isEmpty()){
            if (method_exists($model, $method))return $model->{$method}();
            else return $model->{$method_generic}();
        } else {
            if (method_exists($model, $method)) return  $model->{$method}($this->Data);
            else return $model->{$method_generic}($this->Data->toArray());
        }
    }
}