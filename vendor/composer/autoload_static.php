<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit068204cdb6944b2b86af650f04ee9830
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static $classMap = array (
        'PiramideUploader' => __DIR__ . '/../..' . '/piramide-uploader/PiramideUploader.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit068204cdb6944b2b86af650f04ee9830::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit068204cdb6944b2b86af650f04ee9830::$classMap;

        }, null, ClassLoader::class);
    }
}