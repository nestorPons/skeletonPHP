<?php namespace core; 
use \libs\{Auth};
/***  
 * Clase seguridad de la aplicación.
 * Se utiliza un Json Web Token de seguridad pasado en la cabecera. 
 */
class Security extends Auth{
    // Clases que están descartadas de la autentificación
    private static $zones = ['Login', 'Company', 'Loginrememberpassword']; 

    static function getZones()
    {
        return self::$zones; 
    }  
    static function getJWT() : string
    {
        return $_SERVER['HTTP_JWT']??false;
    }
    static function isRestrict(string $zone) : bool
    {
        return !in_array($zone, self::$zones);
    }
}