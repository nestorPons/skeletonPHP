<?php

namespace core;

use MatthiasMullie\Minify;

class Prepocessor
{
    use ToolsComponents;

    // Constantes globales de las rutas de la aplicación 
    // (app/config/routes.php)
    const
        INCLUDE_ASSETS = \CONFIG['include_assets'], // Incrusta el js y css en la entrada principal
        MAIN_PAGE = \FOLDER\VIEWS . \CONFIG['main'],
        BUNDLE_CSS =  \FILE\BUNDLE_CSS,
        BUNDLE_JS =  \FILE\BUNDLE_JS,
        PUBLIC_FOLDER = \FOLDER\PUBLIC_FOLDER,
        CACHE_FILE = \CACHE\VIEWS,
        FOLDERS_NATIVE_VIEWS = \FOLDER\VIEWS,
        FOLDERS_EXCEPTIONS = []; //[\APP\VIEWS\COMPONENTS]; // Rutas excluidas del preprocesado

    private
        $el, // clase Tag -> Elemento html del archivo procesado
        $cache,
        $isModified = false,
        $content,
        $queueJS = [],
        $loadeds = [],
        $bc;

    function __construct(bool $cacheable = true)
    {
        // Se eliminan todos los archivos de la carpeta build (reinicializa)

        $this->deleteDirectory(self::PUBLIC_FOLDER);

        // Indicamos si cacheamos el proceso
        $this->cacheable = $cacheable;
        $this->cache = (file_exists(self::CACHE_FILE)) ? parse_ini_file(self::CACHE_FILE) : [];

        // Reseteamos los archivos de construccion
        if (file_exists(self::BUNDLE_CSS)) unlink(self::BUNDLE_CSS);
        if (file_exists(self::BUNDLE_JS)) unlink(self::BUNDLE_JS);
        if (!file_exists(self::PUBLIC_FOLDER)) mkdir(self::PUBLIC_FOLDER, 0775, true);

        // Compilamos los archivos js
        $this->listar(\FOLDER\JS);
        // Compilamos los archivos css
        $this->listar(\FOLDER\STYLES);

        // Inicia compilacion de los archivos
        // Primero aseguramos la carga de los componentes
        $this->show_files(self::FOLDERS_NATIVE_VIEWS);

        $this->cache_record($this->cache);
    }
    /**
     * Funcion preprocesadora de los archivos
     * Lee archivos de directorios y los directorios anidados
     */
    private function show_files(String $path): void
    {
        if (!in_array($path, self::FOLDERS_EXCEPTIONS)) {
            $dir = opendir($path);
            while ($current = readdir($dir)) {
                if ($current != "." && $current != "..") {
                    $build_path = str_replace(self::FOLDERS_NATIVE_VIEWS, '', $path . $current);
                    $file = $path . $current;

                    $this->file = $file;
                    $file_build =  self::PUBLIC_FOLDER . $build_path;
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
        // Obtenemos el contenido del archivo y se instancia en una clase Tag
        $this->get_content($file);
           
        // Quitamos los comentarios 
        $this->el->clear();

        // No se la aplicamos a los componentes para que mantengan la encapsulación
        // Y si se ha declarado el atributo compile en false es pq no se desea que se precompile el elemento.   
        $compile = ($this->el->attrs('compile')==false);
        if (!$this->is_component() && $compile) $this->sintax();

        // Construimos el build.js con todas las clases
        $this->build_js();

        if ($file == self::MAIN_PAGE) {
            if (self::INCLUDE_ASSETS) {
                $queueCSS = file_get_contents(self::BUNDLE_CSS);
                $queueCSS = '<style type="text/css">' . $queueCSS . '</style>';
                $content_JS = file_get_contents(self::BUNDLE_JS);
                $content_JS = '<script>' . $content_JS . '</script>';
            } else {
                $queueCSS = '<link rel="stylesheet" href="' . self::BUNDLE_CSS . '">';
                $content_JS = "<script src='" . self::BUNDLE_JS . "'></script>";
            }
            $this->el->replace('</head>', $queueCSS . '</head>');
            $this->el->replace('</body>', $content_JS . '</body>');
        }

        // Compresión salida html
        // AKI:: Descomentar la siguiente linea 
        // $this->compress_code();

        file_put_contents($file_build, $this->el->element());

        return $this;
    }
    // Obtiene el contenido del archivo y crea el tag principal 
    private function get_content(String $file): self
    {
        $this->el = new Tag(file_get_contents($file));
        return $this;
    }
    /** 
     * Funcion que aplica una sintaxis propia  a las vistas
     * Proceso de compilación de las plantillas
     */
    private function sintax(): self
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
            $tag->delAttr('scoped');
            $tag->delAttr('lang');
        }
        // Encapsulación de los scripts
        foreach ($this->tags('script') as $tag) {
            $this->add_script_scope($tag);
            $tag->delAttr('scoped');
        }

        return $this;
    }
    /**
     * Buscamos componentes principales en el html los posibles anidos se pasan por string al componente
     */
    private function declare_component(): self
    {

        foreach ($this->search_components($this->el->body()) as $tag) {

            $content = $tag->body() ?: 'null';  
            $str_content = addslashes($content);
    
            $JSON_Attr = json_encode($tag->attrs());
            
            $this->el->replace(
                $tag->code(),
                "<?php \$c = new \core\Component('{$tag->type()}', '$JSON_Attr', '$str_content'); \$c->print();?>"
            );
        }

        return $this;
    }
    /**
     * Sintaxis para @if() ... @endif
     */
    private function sintax_if(): self
    {

        $regex_conditional = '/[^\'\`\"]@if(\s)*?\((.)*?\)(.)*?@endif/sim';
        $start_condition = '/@if(\s)*?\((.)*?\)/sim';
        $midle_condition = '/@else(\s)*?\((.)*?\)/sim';
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
                        // Buscamos la segunda condición si existe

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
        return ($this->path == \FOLDER\COMPONENTS);
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
            $len = preg_match_all('/[^\`\'\"]@for\s*\((.*?)\)(.*?)@endfor/sim', $this->el->body(), $matches)
        ) {
            for ($i = 0; $i < $len; $i++) {
                $cond = $matches[1][$i];
                $struct = $matches[0][$i];

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
    /**
     * Minifica el Less y transforma a css
     */
    private function less_compiler(String $content): String
    {
        //COMPILAMOS LESS
        $less = new \lessc;
        return $less->compile($content);
    }
    /**
     * Minifica el css 
     */
    private function css_minify(String $content): String
    {
        // MINIMIFICAMOS
        $minifier = new Minify\CSS;
        $minifier->add($content);
        return $minifier->minify();
    }
    /**
     * 
     */
    private function less(String $content): self
    {
        //COMPILAMOS LESS
        $less = new \lessc;
        $content_less = $less->compile($content);
        // MINIMIFICAMOS
        $minifier = new Minify\CSS;
        $minifier->add($content_less);
        $content_min = $minifier->minify();

        $this->el->replace($content, $content_min);

        return $this;
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
         * 2 -> argumentos
         * 3 -> contenido
         */
        if (
            $len = preg_match_all($regex, $this->el->body(), $matches)
        ) {
            for ($i = 0; $i < $len; $i++) {
                $a[$i] = new Tag($matches[0][$i]);
            }
        }
        return @$a ?: [];
    }
    /**
     * Procesa la sintaxis de los elementos @include() → forma simplificada de include de php
     */
    private function includes(): self
    {
        $len = preg_match_all('/[^\`\"\'\#](\@include\s*?\((.*)\))/', $this->el->body(), $matches);
        if ($len) {
            for ($i = 0; $i < $len; $i++) {
                $file = $matches[2][$i];
                $search = $matches[1][$i]; 
                // Sintaxis para las variable cargadas desde los controladores
                if (
                    $len2 = preg_match_all('/\$\$(\w+\-?\w*)/is', $file, $match)
                ) {
                    for ($j = 0; $j < $len2; $j++) {
                        str_replace($match[0][$j], "\$_FILES['{$match[1][$j]}']", $file);
                    }
                }
                $file = self::PUBLIC_FOLDER . $file;
                
                $this->el->replace($search, "<?php include('{$file}')?>");
            }
        }
        return $this;
    }
    /**
     * Busca sibolo $$ para y lo reemplaza por variables php
     */
    private function sintax_vars(): self
    {
        $content = $this->el->body();
        if (
            preg_match_all('#[^\`\"\'](\$\$(\w+\-?\w*))#is', $content, $matches)
        ) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $str = '<?=$' . trim($matches[2][$i] ?: null, '\$') . '?>';
                $content = str_replace($matches[1][$i], $str, $content);
            }
            $this->el->body($content);
        }
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
    private function load_class_childrens(): void
    {
        foreach ($this->queueJS as $key => $value) {
            if (in_array($value[1], $this->loadeds)) {
                $this->load_class_js($value[2]);
            }
        }
    }
    private function cache_record(array $cache): bool
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
    /**
     * Resetea el contenido de las carpetas del proyecto 
     */
    private function deleteDirectory(string $dir): self
    {
        if (!$dh = @opendir($dir)) {
            var_dump($dir);
            mkdir($dir, 0777, true); // Si no la encuentra lo crea
        } else {
            while (false !== ($current = readdir($dh))) {
                if ($current != '.' && $current != '..') {
                    if (!@unlink($dir . '/' . $current))
                        $this->deleteDirectory($dir . '/' . $current);
                }
            }
            closedir($dh);
        }
        return $this;
    }
    /**
     * Minifica, comprime y agrupa el contenido JS 
     */
    private function listar(string $path): void
    {
        $arr_files = [];
        $str_js = '';
        $str_css = '';
        if ($folder = opendir($path)) {
            while (false !== ($file = readdir($folder))) {
                // Filtramos directorios padres
                if ($file != '..' && $file != '.') {
                    // Comprobamos si es un archivo
                    if (is_dir($path . $file)) $this->listar($path . $file . '/');
                    $arr_files[] = $file;
                }
            }
            // Ordenamos los archivos para respetar la propiedad de cascada en los estilos
            sort($arr_files);
            foreach ($arr_files as $file) {
                // Comprobamos que sea un archivo js 
                $ex = explode('.', $file);
                $ext = end($ex);
                if (isset($ext)) {
                    if ($ext === 'js') {
                        if ($this->checkedCompile($path . $file, $path . "{$ext[0]}.min.js")) {
                            $minifier_JS = new Minify\JS($path . $file);
                            $str_js .= $minifier_JS->minify() . ';';
                        }
                    } elseif ($ext === 'less' || $ext === 'css') {
                        // Archivos de estilos less
                        $content = file_get_contents($path . $file) or die('No se puede abrir el archivo:' . $file);
                        if ($ext === 'less') $content = $this->less_compiler($content);
                        $compile = $this->css_minify($content);
                        $str_css .= $compile;
                    }
                }
            }
            // Se crea el archivo único para JS 
            $file_handle = fopen(self::BUNDLE_JS, 'a+');
            fwrite($file_handle, $str_js);
            fclose($file_handle);
            // Se crea el archivo único para CSS 
            $file_handle = fopen(self::BUNDLE_CSS, 'a+');
            fwrite($file_handle, $str_css);
            fclose($file_handle);
        }
    }
    /**
     * Comprueba la fecha de la ultima compilación si es mas nueva que el archivo compilado
     */
    function checkedCompile($in, $out)
    {
        if (file_exists($out)) return (filemtime($in) > filemtime($out));
        return true;
    }
}
