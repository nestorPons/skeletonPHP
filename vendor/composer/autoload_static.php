<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9af3f9963c563740eab98ea83558b01a
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'models\\' => 7,
        ),
        'l' => 
        array (
            'libs\\' => 5,
        ),
        'h' => 
        array (
            'helpers\\' => 8,
        ),
        'c' => 
        array (
            'core\\' => 5,
            'controllers\\' => 12,
        ),
        'M' => 
        array (
            'MatthiasMullie\\PathConverter\\' => 29,
            'MatthiasMullie\\Minify\\' => 22,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/models',
        ),
        'libs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/libs',
        ),
        'helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/helpers',
        ),
        'core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/core',
        ),
        'controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/controllers',
        ),
        'MatthiasMullie\\PathConverter\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/path-converter/src',
        ),
        'MatthiasMullie\\Minify\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/minify/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'lessc' => __DIR__ . '/..' . '/leafo/lessphp/lessc.inc.php',
        'lessc_formatter_classic' => __DIR__ . '/..' . '/leafo/lessphp/lessc.inc.php',
        'lessc_formatter_compressed' => __DIR__ . '/..' . '/leafo/lessphp/lessc.inc.php',
        'lessc_formatter_lessjs' => __DIR__ . '/..' . '/leafo/lessphp/lessc.inc.php',
        'lessc_parser' => __DIR__ . '/..' . '/leafo/lessphp/lessc.inc.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9af3f9963c563740eab98ea83558b01a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9af3f9963c563740eab98ea83558b01a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9af3f9963c563740eab98ea83558b01a::$classMap;

        }, null, ClassLoader::class);
    }
}
