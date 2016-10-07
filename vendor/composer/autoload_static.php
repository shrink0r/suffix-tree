<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit33fc7be142031e3f2731bc097b06bd88
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Shrink0r\\SuffixTree\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Shrink0r\\SuffixTree\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit33fc7be142031e3f2731bc097b06bd88::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit33fc7be142031e3f2731bc097b06bd88::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
