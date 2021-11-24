<?php
/**
 *
 * Licencias
 *
 * Se concede permiso por la presente, libre de cargos, a cualquier persona que obtenga
 * una copia de este software y de los archivos de documentación asociados (el "Software"),
 * a utilizar el Software sin restricción, incluyendo sin limitación los derechos a usar, copiar,
 * modificar, fusionar, publicar, distribuir, sublicenciar, y/o vender copias del Software, y a
 * permitir a las personas a las que se les proporcione el Software a hacer lo mismo, sujeto a
 * las siguientes condiciones:
 * 
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o
 * partes sustanciales del Software.
 * 
 * EL SOFTWARE SE PROPORCIONA "COMO ESTÁ", SIN GARANTÍA DE NINGÚN
 * TIPO, EXPRESA O IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A GARANTÍAS
 * DE COMERCIALIZACIÓN, IDONEIDAD PARA UN PROPÓSITO PARTICULAR E
 * INCUMPLIMIENTO. EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE LOS
 * DERECHOS DE AUTOR SERÁN RESPONSABLES DE NINGUNA RECLAMACIÓN,
 * DAÑOS U OTRAS RESPONSABILIDADES, YA SEA EN UNA ACCIÓN DE
 * CONTRATO, AGRAVIO O CUALQUIER OTRO MOTIVO, DERIVADAS DE, FUERA DE
 * O EN CONEXIÓN CON EL SOFTWARE O SU USO U OTRO TIPO DE ACCIONES EN
 * EL SOFTWARE.
 * 
 * @author    Nestor Pons <nestorpons@gmail.com>
 * @copyright 2019 Nestor Pons y contribuidores
 * @license   https://opensource.org/licenses/MIT
 */

use \core\{Router, Prepocessor};

// Ruteado de todas los archivos del proyecto 
define('ROOT' ,dirname(__DIR__) . '/');
require_once ROOT . 'app/config/routes.php';
require_once ROOT . 'vendor/autoload.php';  
define('CONFIG', parse_ini_file(\FILE\CONFIG));

date_default_timezone_set("UTC");
date_default_timezone_set(\CONFIG['timezone']);

// Desarrollo
if( CONFIG['develop'] ){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');
    require_once \FOLDER\HELPERS  . 'dev.php';
    
    new Prepocessor(false);
}

// Si es primera carga (No contiene ningina petición por parametros) 
// incluye la pagina principal

if(!$_REQUEST) include(\FOLDER\PUBLIC_FOLDER . \CONFIG['main']);
// Caso contrario se devuelve un objeto enrutado
else new Router($_REQUEST);