<?php

// autoload_real.php generated by Composer

class ComposerAutoloaderInitbfa6ac7b5766eae1eb7803a17c130bfb
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitbfa6ac7b5766eae1eb7803a17c130bfb', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInitbfa6ac7b5766eae1eb7803a17c130bfb', 'loadClassLoader'));

        $vendorDir = dirname(__DIR__);
        $baseDir = dirname($vendorDir);

        $map = require __DIR__ . '/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->set($namespace, $path);
        }

        $classMap = require __DIR__ . '/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        require $vendorDir . '/tronghieu/flywheel-framework/Flywheel/Common.php';
        require $vendorDir . '/meenie/javascript-packer/class.JavaScriptPacker.php';
        require $vendorDir . '/meenie/munee/config/bootstrap.php';
        require $vendorDir . '/swiftmailer/swiftmailer/lib/swift_required.php';

        return $loader;
    }
}
