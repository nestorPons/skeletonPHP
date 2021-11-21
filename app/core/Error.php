<?php namespace core;

class Error extends \Exception{
    public static $last = false;

    const E000 = "Error inesperado";

    const E001 = "No se pudo crear la conexion a la base de datos" ;
    const E003 = "No se pudo guardar el registro";
    const E004 = "Registro duplicado";
    const E005 = "Error faltan datos";
    const E006 = "Error en el envio de los datos";
    const E007 = "Error de seguridad";
    const E008 = 'No se pudo cargar la configuracion'; 
    const E009 = 'Error tipo de datos';
    
    // Creando empresa
    const E011 = "Esta empresa ya existe" ;
    const E012 = "No se pudo validar los datos";
    const E013 = "No se pudo crear la base de datos";
    const E014 = "No se pudo crear las tablas";
    const E015 = "No se ha podido crear la empresa";
    const E016 = "No se pudo inicializar las tablas";
    const E017 = "No se pudo crear el archivo de la empresa";
    const E018 = 'No se pudo encontrar la empresa solicitada';
    const E019 = "No se ha podido registrar el usuario";
    
    //login 
    const E022 = "Email ocupado."; 
    const E023 = "Usuario desactivado \n Consulte con el administrador. ";
    const E024 = "Cuenta bloqueada. Consulte su administrador.";
    const E025 = "No se ha encontrado el usuario";
    const E026 = "Usuario o contraseña incorrectos.";
    const E027 = "Pin incorrecto";
    const E028 = "Usuario desactivado \n Acceda a recordar contraseña para activar la cuenta. ";

    //Sessiones y times
    const E050 = "Se ha excedido el tiempo de sessión";
    const E010 = "Ha expirado la sessión" ;
    
    //Tokens 
    const E061 = "Su token ha expirado";
    const E062 = "Los tokens no coinciden";

    //Formularios
    const E030 = "Error guardando datos";
    const E031 = "Tamaño de datos incorrecto";

    // Errores relacionados plan contratado
    const E041 = "Su plan no admite más agendas \n Pongase en contacto con el administrador. "; 

    //Base datos
    const E051 = "No se encontro la base de datos";
    const E052 = "No se pudo conectar con la base de datos";
    const E053 = "Error guardando los datos";
    const E054 = "Registro existente \n no se puede sobreescribir";
    
    // Clases
    const E060 = "No se han registrado los datos corretamente";
    
    //Mail 
    const E071 = "No se ha podido mandar el email";

    function __construct(string $code = null, string $message = '', \Exception $previous = null){   
        if($code) {
            $this->array($code);
            parent::__construct(constant('self::'.$code), intval($code), $previous);
        }
    }

    public static function array($err, $obs = null){
        if (defined ('self::'.$err))
            return ['success'=>0 , 'code' => $err , 'mens' => constant('self::'.$err), 'obs' => $obs] ;
        else
            return ['success'=>0 , 'code' => 'E000' , 'mens' => $err, 'obs' => $obs] ;
    }
    public static function set($err){
        self::$last = $err;
        return false;
    }
    public static function getLast(){
        $err = self::$last;
        self::$last = false;
        return $err;
    }
    // Detiene el script y devuelve un array de error
    public static function die($err = null, $mens = null){
        $err = $err??self::getLast();
        die(json_encode(self::array($err, $mens)));
    }
    public static function toString($err = null){
        $err = $err??self::getLast();
        print_r(constant('self::'.$err));
    }
}