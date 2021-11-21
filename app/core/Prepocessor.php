<?php

namespace core;

use MatthiasMullie\Minify;

class Prepocessor
{
    use ToolsComponents;

    // Constantes globales de las rutas de la aplicación 
    // (app/config/routes.php)
    const
        BUNDLE_JS =  \FILE\BUNDLE_JS, 
        BUILD = \FOLDER\PUBLIC_FOLDER,
        CACHE_FILE = \CACHE\VIEWS,
        FOLDERS_NATIVE_VIEWS = \FOLDER\VIEWS,
        MAIN_PAGE = \FOLDER\VIEWS . \CONFIG['main'],
        FOLDERS_EXCEPTIONS = []; //[\APP\VIEWS\MYCOMPONENTS]; // Rutas excluidas del preprocesado

    private
        $el, // clase Tag -> Elemento html del archivo procesado
        $cache,
        $isModified = false,
        $content,
        $queue,
        $queueJS = [],
        $loadeds = [],
        $bc;                        // var de proteccion para buclesº

    function __construct(bool $cacheable = true)
    {
        // Se eliminan todos los archivos de la carpeta build (reinicializa)
        $this->deleteDirectory(self::BUILD);
        
        

        // Indicamos si cacheamos el proceso
        $this->cacheable = $cacheable;
        $this->cache = (file_exists(self::CACHE_FILE)) ? parse_ini_file(self::CACHE_FILE) : [];

        // Reseteamos el archivo de construccion build de js para agrupar todo el codigo js minificado
        if (file_exists(self::BUNDLE_JS)) unlink(self::BUNDLE_JS);
        if (!file_exists(self::BUILD)) mkdir(self::BUILD, 0775, true);

        // Compilamos los archivos js
        $this->listar(\FOLDER\JS);
        //Añade el link del bundle js
        $this->queue = "<script src='" . self::BUNDLE_JS . "'></script>";
                
        // Inicia compilacion de los archivos
        // Primero aseguramos la carga de los componentes
        $this->show_files(self::FOLDERS_NATIVE_VIEWS);

        $this->cache_record($this->cache);
    }
    // Funcion preprocesadora de los archivos
    // Lee archivos de directorios y los directorios anidados
    private function show_files(String $path)
    {
        if (!in_array($path, self::FOLDERS_EXCEPTIONS)) {
            $dir = opendir($path);
            while ($current = readdir($dir)) {
                if ($current != "." && $current != "..") {
                    $build_path = str_replace(self::FOLDERS_NATIVE_VIEWS, '', $path . $current);
                    $file = $path . $current;
                    #pr($file);
                    $this->file = $file;
                    $file_build =  self::BUILD . $build_path;
                    $this->path = $path;
                    
                    if (is_dir($file)) {
                        // DIRECTORIOS 
                        if (!file_exists($file_build)) mkdir($file_build, 0775, true);
                        $this->show_files($file . '/');
                    } else {
                        // ARCHIVOS
                        if (!file_exists($file_build)) {
                            $this->build($file, $file_build);
                        }
                    }
                }
            }
            // Cargamos las clases js hijas que no se pudieron cargar anteriormente
            $this->load_class_childrens();
        }
    }
    /**
     * Publica la aplicación en la carpeta build
     */
    private function build($file, $file_build): self
    {
        // Obtenemos el ontenido del archivo se instancia Tag
        $this->get_content($file);

        // Quitamos los comentarios 
        $this->el->clear();

        // No se la aplicamos a los componentes para que mantengan la encapsulación
        if (!$this->is_component()) $this->sintax();

        // Construimos el build.js con todas las clases
        $this->build_js();

        if ($file == self::MAIN_PAGE) $this->queue();

        // Compresión salida html
        //$this->compress_code();

        file_put_contents($file_build, $this->el->element());

        return $this;
    }
    // Obtiene el contenido del archivo y crea el tag principal 
    private function get_content(String $file): self
    {
        $this->el = new Tag(file_get_contents($file));
        return $this;
    }
    // Funcion que aplica una sintaxis propia  a las vistas
    // Proceso de compilación de las plantillas
    private function sintax() : self
    {
        // Añadimos el id al documento
        $this->el->replace('--id', $this->el->id());

        $this->sintax_if();
        $this->sintax_for();
        $this->includes();
        // Busca componentes 1 nivel de anidamiento y remplaza
        $this->declare_component();
        $this->sintax_vars();

        // Encapsulación de los estilos
        foreach ($this->tags('style') as $tag) {

            $this->add_style_scope($tag);

            if ($tag->get('lang') == 'less') {
                $this->less($tag->body());
            }
            // eliminamos el argumento scoped
            $tag->del('scoped');
            $tag->del('lang');
        }
        // Encapsulación de los scripts
        foreach ($this->tags('script') as $tag) {
            $this->add_script_scope($tag);
            $tag->del('scoped');
        }

        return $this; 
    }
    /**
     * Buscamos componentes principales en el html los posibles anidos se pasan por string al componente
     */
    private function declare_component() : self
    {
        foreach ($this->search_components($this->el->body()) as $tag) {

            $content = $tag->body() ?? 'null';
            $str_content = addslashes($content);

            $str_at = json_encode($tag->attrs());

            $this->el->replace(
                $tag->code(),
                "<?php \$c = new \core\Component('{$tag->type()}', '$str_at', '$str_content'); \$c->print();?>"
            );
        }

        return $this;
    }
    /**
     * Sintaxis para @if() ... @endif
     */
    private function sintax_if(): self
    {

        $regex_conditional = '/@if(\s)*?\((.)*?\)(.)*?@endif/sim';
        $start_condition = '/@if(\s)*?\((.)*?\)/sim';
        $end_condition = '/@endif/i';;

        if (
            preg_match_all($regex_conditional, $this->el->body(), $matches)
        ) {
            foreach ($matches[0] as $value) {
                // Se obtiene la condición
                if (preg_match($start_condition, $value, $matches)) {
                    $condition = preg_replace('/@if(\s)*?\(/sim', '', $matches[0]);
                    $condition = preg_replace('/\)$/', '', $condition);
                    if (empty($condition)) $condition = null;
                    $valcon = false;
                    eval('if ($condition) { $valcon = true; }');
                    if ($valcon) {
                        // Imprimimos el contenido dentro del condicional
                        $replace = preg_replace($start_condition, '', $value);
                        $replace = preg_replace($end_condition, '', $replace);
                        $this->el->replace($value, $replace);
                    } else {
                        // Eliminamos todo el condicional 
                        $this->el->replace($value, '');
                    }
                }
            }
        }
        return $this;
    }
    /**
     * Comprueba si es un componente comparando su ruta
     */
    private function is_component(): bool
    {
        return ($this->path == \FOLDER\MYCOMPONENTS);
    }
    private function add_script_scope(Tag $tag): self
    {
        if ($tag->get('scoped')) {
            $lastContent = $tag->body();
            $tag->body(
                "(function(){
                   $lastContent
               })()"
            );
            $this->el->replace($lastContent, $tag->body());
        }
        return $this;
    }
    private function add_style_scope(Tag $tag): self
    {
        if ($tag->get('scoped')) {
            $lastContent = $tag->body();

            // Quitamos las reglas principales
            $content = $tag->body();
            //$content = preg_replace('/@import.*?;/', '', $content);
            //$content = preg_replace('/@charser.*?;/', '', $content);
            // Se coloca el id a los estilos 
            $tag->body("#{$this->el->id()}{{$content}}");

            $this->el->replace($lastContent, $tag->body());
        };
        return $this;
    }
    /**
     * Procesado de la sintaxis @for() ... @endfor
     */
    private function sintax_for(): self
    {
        if (
            $len = preg_match_all('/@for\s*\((.*?)\)(.*?)@endfor/sim', $this->el->body(), $matches)
        ) {
            for ($i = 0; $i < $len; $i++) {
                $res = '';
                $cond = $matches[1][$i];
                $body = $matches[2][$i];
                $struct = $matches[0][$i];

                // Formato {"a":1,"b":2,"c":3,"d":4,"e":5} sin comillas exteriores
                // Si la condcion tiene $$valor transformarlo en $valor

                $s = preg_replace('/\@for\(.*?\)/i', '<?php foreach($' . ltrim($cond, '$') . ' as $key => $value):?>', $struct);
                $s = str_replace('$$value', '<?=$value?>', $s);
                $s = str_replace('$$key', '<?=$key?>', $s);
                $s = str_replace('@endfor', '<?php endforeach?>', $s);
                $this->el->replace($struct, $s);
            }
        }
        return $this;
    }
    private function compress_code(): self
    {
        $search = array(
            '/\>[^\S ]+/s',  // remove whitespaces after tags
            '/[^\S ]+\</s',  // remove whitespaces before tags
            '/(\s)+/s'       // remove multiple whitespace sequences
        );

        $replace = array('>', '<', '\\1');
        $this->el->element(preg_replace($search, $replace, $this->el->element()));
        return $this;
    }
    private function less(String $content)
    {
        //COMPILAMOS LESS
        $less = new \lessc;

        $content_less = $less->compile($content);
        // MINIMIFICAMOS
        $minifier = new Minify\CSS;
        $minifier->add($content_less);
        $content_min = $minifier->minify();

        $this->el->replace($content, $content_min);
    }
    /**
     *   Devuelve todos los argumentos de un tag
     *  @return array de la clase Tag
     */
    private function tags(string $tag): array
    {
        $regex = "/\<($tag) ([^>]*?)>(.*?)<\/\\1>/si";
        /**
         * 0 -> Todo
         * 1 -> tag
         * 2 -> argimentos
         * 3 -> contenido
         */
        if (
            $len = preg_match_all($regex, $this->el->body(), $matches)
        ) {
            for ($i = 0; $i < $len; $i++) {
                $a[$i] = new Tag($matches[0][$i]);
            }
        }
        return $a ?? [];
    }
    // Procesa la sintaxis de los elementos @include()
    private function includes(): self
    {
        if (
            $len = preg_match_all('/\@include\s*\((.*?)\)\s/', $this->el->body(), $matches)
        ) {
            for ($i = 0; $i < $len; $i++) {
                $body = $matches[1][$i];
                // Sintaxis para las variable cargadas desde los controladores
                if (
                    $len2 = preg_match_all('/\$\$(\w+\-?\w*)/is', $body, $match)
                ) {
                    for ($j = 0; $j < $len2; $j++) {
                        $body = str_replace($match[0][$j], "\$_FILES['{$match[1][$j]}']", $body);
                    }
                }
                $this->el->replace($matches[0][$i], "<?php include($body)?>");
            }
        }
        return $this;
    }
    // Busca sibolo $$ para y lo reemplaza por variables php
    private function sintax_vars(): self
    {
        $content = $this->el->body();
        if (
            preg_match_all('#\$\$(\w+\-?\w*)#is', $content, $matches)
        ) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $str = '<?=$_FILES["' . trim($matches[1][$i] ?? null, '\$') . '"]?>';
                $content = str_replace($matches[0][$i], $str, $content);
            }
            $this->el->body($content);
        }

        return $this;
    }
    private function queue(): self
    {
        $this->el->replace('</body>', $this->queue . '</body>');
        return $this;
    }
    /**
     * Extraemos las clases de los componentes 
     * y las cargamos en un ambito global
     */
    private function build_js()
    {
        $this->bc = 0;
        if (
            $tags = $this->el->search('script')
        ) {
            foreach ($tags as $tag) {
                $class_js = $tag->body();
                // Buscamos las clases
                if (preg_match_all('/ class (\w*?).*{/i', $class_js, $matches)) {
                    if (
                        // Comprueba si la clase extiende de alguna otra
                        $len = preg_match_all('/ (\w*?) extends (\w*?)\s*{/i', $class_js, $matches)
                    ) {
                        for ($i = 0; $i < $len; $i++) {
                            if (in_array($matches[2][$i], $this->loadeds)) {
                                $this->load_class_js($class_js);
                            } else {
                                /*
                                0 => nombre de la clase
                                1 => nombre de la clase padre 
                                2 => todo el contenido
                                */
                                $this->queueJS[] = [$matches[1][$i], $matches[2][$i], $class_js];
                            }
                        }
                    } else {
                        $this->load_class_js($class_js);
                    }
                    // Eliminamos la clase del documento html
                    $this->el->unset($tag);
                }
            }
        }
    }
    private function load_queueJS()
    {
        do {
            $this->bc++;
            foreach ($this->queueJS as $key => $value) {
                if (in_array($value[1], $this->loadeds)) {
                    $this->load_class_js($value[2]);
                    unset($this->queueJS[$key]);
                }
            }
            // Mensaje de error de clase extendida no encontrada
            if ($this->bc > 10) {
                die("ERROR!! <br> La clase js {$value[0]}, no ha podido ser cargada!!");
                break;
            }
        } while (count($this->queueJS) > 0);
    }
    /**
     * Carga las clases al archivo bundle.js
     */
    private function load_class_js($class_js)
    {
        if (preg_match('/class (\w*){1}/si', $class_js, $matches)) {
            if (!in_array($matches[1], $this->loadeds)) {
                // MINIMIFICAMOS JS
                $minifier = new Minify\JS;
                $minifier->add($class_js);
                file_put_contents(self::BUNDLE_JS, $minifier->minify(), FILE_APPEND);

                // Registramos la clase como cargada 
                $this->loadeds[] = $matches[1];
            }
        }
    }
    /**
     * Carga de las clases hijas js
     */
    private function load_class_childrens()
    {
        foreach ($this->queueJS as $key => $value) {
            if (in_array($value[1], $this->loadeds)) {
                $this->load_class_js($value[2]);
            }
        }
    }
    private function cache_record(array $cache)
    {
        if ($this->isModified) {
            $out = '';
            foreach ($cache as $k => $v) {
                $out .= $k . ' = "' . $v . '"' . "\n";
            }
            file_put_contents(self::CACHE_FILE, $out, LOCK_EX);
            return true;
        } else return false;
    }
    private function deleteDirectory($dir)
    {
        if (!$dh = @opendir($dir)) {
            // Si no lo encuentra lo crea
            mkdir($dir, 0777);
        } else {
            while (false !== ($current = readdir($dh))) {
                if ($current != '.' && $current != '..') {
                    if (!@unlink($dir . '/' . $current))
                        $this->deleteDirectory($dir . '/' . $current);
                }
            }
            closedir($dh);
        }
    }
    private function listar($path){
        //Funcion que comprueba la fecha de la ultima compilación si es mas nueva que el archivo compilado
        function checkedCompile($in, $out) {
            if(file_exists($out)) return (filemtime($in) > filemtime($out));
            return true;
        }
        if ($folder = opendir($path)) {
            while (false !== ($file = readdir($folder))) {
                // Filtramos directorios padres
                if($file != '..' && $file != '.' ){
                    // Comprobamos si es un archivo
                    if(is_dir($path.$file)) $this->listar($path.$file.'/'); 
                    // Comprobamos que sea un archivo js 
                    $ext = explode('.',$file);
                    $content_minify = '';
                    if(isset($ext[1]) && $ext[1] === 'js'){
                        if(checkedCompile($path . $file, $path . "{$ext[0]}.min.js")){
                            $minifier_JS = new Minify\JS($path . $file);
                            $content_minify .= $minifier_JS->minify() . ';';
                        }
                    }

                    // Se crea el archivo único para JS 
                    $file_handle = fopen(self::BUNDLE_JS , 'a+');
                    fwrite($file_handle,$content_minify);
                    fclose($file_handle);
                }
            }
        }
    }
}
