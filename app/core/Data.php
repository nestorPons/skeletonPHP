<?php

namespace core;

use PhpParser\Node\Expr\Cast\Array_;

/**
 * Clase gestión de datos para los componentes
 */
class Data
{
    /**
     * Los datos que se proporcionan al objeto serán convertidos en atributos del mismo. 
     * @param string|array|object Cualquier tipo de datos. 
     * 
     */
    function __construct($data = null)
    {
        if ($data) {
            if (is_string($data)) {
                if (preg_match("/^\{(.*?)\}$/sim", $data)) $data = json_decode($data, true);
                else {
                    $this->addItem($data);
                    $data = [];
                }
            } elseif (is_object($data)) $data = get_object_vars($data);
            if ($data) {
                foreach ($data as $key => $value) {
                    $this->addItem($value, $key);
                }
            }
        }
    }
    /**
     * Añade un valor y opcionalmente su llave, al objeto Data. 
     */
    function addItem($value, $key = null)
    {
        if ($key) return $this->{$key} = $value;
        else {
            if (is_object($value)) return $this->{get_class($value)} = $value;
            else return $this->{$value} = trim($value);
        }
    }
    /**
     * Añade un array u objeto al objeto Data. 
     */
    function addItems(array $params = null): self
    {
        if ($params) {
            foreach ($params as $key => $value)
                $this->addItem($value, $key);
        }
        return $this;
    }
    function set($arg1, $arg2 = null)
    {
        if ($arg2) return $this->addItem($arg2, $arg1);
        else return $this->addItem($arg1);
    }
    /**
     * Obtiene todos los elementos guardados en formato array
     */
    function getAll(): array
    {
        return $this->toArray();
    }
    function getArray($attr, array $filter = null)
    {
        if (is_object(reset($this))) {
            $arr = [];
            foreach ($this as $obj) {
                if (!$filter) $arr[$obj->id] = $obj->{$attr};
                // Filtramos los datos de salida con un argumento dado en tipo de array clave => valor
                else if ($obj->{key($filter)} == $filter[key($filter)]) $arr[$obj->id] = $obj->{$attr};
            }
            return $arr;
        } else {
            if (array_key_exists($attr, (array)$this)) {
                return $this->data[$attr];
            } else {
                return false;
            }
        };
    }
    /**
     * Validador de los datos
     * $args es un array de los atributos que se quieren validar
     * Si añadimos un tipo a los datos este también seré validado
     */
    function validate(array $args = [], bool $err = false)
    {
        function err($err)
        {
            if ($err) return 'Datos no validos';
            else return false;
        }

        foreach ($args as $value) {
            if (is_array($value)) {
                if (!isset($this->{key($value)}) || gettype($this->{key($value)}) != $value[key($value)]) return err($err);
            } else {
                if (!isset($this->{$value})) return err($err);
            }
        }
        return true;
    }
    function isEmail(string $arg)
    {
        if (!(isset($this->{$arg}) && filter_var($this->{$arg}, FILTER_VALIDATE_EMAIL))) Error::die('E009', $this->{$arg} ?? null);
        return true;
    }
    function isString(string $arg, int $len)
    {
        if (!(isset($this->{$arg}) && strlen($this->{$arg}) < $len)) Error::die('E009', $this->{$arg} ?? null);
        return true;
    }
    /**
     * Combierte los datos en un array
     * @param array $key => array de claves que se desean eliminar
    */  
    function toArray(array $key = null): array
    {
        $data = (array)$this;
        if ($key) {
            foreach ($key as $v) {
                unset($data[$v]);
            }
        }
        return $data;
    }
    /**
     * Método que devuelve los datos en un objeto JSON
     * @return Object JSON
     */
    function toJSON(): object
    {
        $en = json_encode($this->toArray());
        return json_decode($en);
    }
    /**
     * Método que devuelve los datos en un string
     * @return string cadena de texto formato json con los datos almacenados  en el objeto 
     */
    function toString(): string
    {
        return json_encode($this->toArray());
    }

    static function codify(string $arg)
    {
        $originals = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modify = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $arg = str_replace(' ', '_', trim($arg));
        $arg = utf8_decode($arg);
        $arg = strtr($arg, utf8_decode($originals), $modify);
        $arg = strtolower($arg);
        return utf8_encode($arg);
    }
    static function normalize(string $arg)
    {
        $arg = str_replace('_', ' ', trim($arg));
        $arg = ucwords($arg);
        return $arg;
    }
    /* Eliminamos datos del objeto
    * @param puede ser array de strings (elimina varios) o string elimina solo uno. 
    * si es array retorna el numero de eliminados 
    * si es string retorno booleano true si ha eliminado o false si no lo ha encontrado
    */
    function delete($arg)
    {
        if (is_array($arg)) {
            $count = 0;
            foreach ($arg as $a) {
                if (property_exists($this, $a)) {
                    unset($this->{$a});
                    $count++;
                };
            }
            return $count;
        } else if (is_string($arg)) {
            if (property_exists($this, $arg)) {
                unset($this->{$arg});
                return true;
            } else return false;
        } else {
            return false;
        }
    }
    // Usa un atributo y lo destruye 
    function use(string $arg)
    {
        if (isset($this->{$arg})) {
            $attr =  $this->{$arg};
            $this->delete($arg);
            return $attr;
        } else return false;
    }
    function normalizeAttr(string $attr)
    {
        return $this->{$attr} = $this->normalize($this->{$attr});
    }
    function codifyAttr(string $attr)
    {
        return $this->{$attr} = $this->codify($this->{$attr});
    }
    // Quitas las propiedades del objeto dado
    // obj => objeto con las propiedades a eliminar
    function filter($obj)
    {
        foreach ($this as $key => $val) {
            if (!property_exists($obj, $key)) {
                unset($this->{$key});
            }
        }
        return $this;
    }
    // Comprueba si alguna propiedad esta vacia
    // O si no se pasan parametros si el objeto tiene propiedades
    function isEmpty(String $prop = ''): bool
    {
        if ($prop !== '') {
            if (isset($this->{$prop})) {
                return empty($this->{$prop});
            } else return true;
        } else {
            return empty(get_object_vars($this));
        }
    }
}
