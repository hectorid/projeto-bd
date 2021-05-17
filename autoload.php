<?php

use const App\DIR_CLASSES;

require_once 'defines.php';

set_include_path(get_include_path() . ':' . DIR_CLASSES);

spl_autoload_register(function ($classname) {
    $classname = str_replace('App\\', '', $classname); // Removes the App\ prefix
    $classname = str_replace('\\', DIRECTORY_SEPARATOR, $classname);

    $fullpath = DIR_CLASSES . DIRECTORY_SEPARATOR . "{$classname}.php";

//    var_dump($fullpath);

    include_once $fullpath;
});
