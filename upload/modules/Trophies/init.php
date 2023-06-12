<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *
 *  Trophies module initialisation file
 */

// Initialise trophies language
$trophies_language = new Language(ROOT_PATH . '/modules/Trophies/language', LANGUAGE);

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'modules', 'Trophies', 'classes', $class . '.php']);
    if (file_exists($path)) {
        require_once($path);
    }
});

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Trophies', 'classes', 'Events', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

// Load classes
spl_autoload_register(function ($class) {
    $class_path = str_replace('\\', '/', $class);
    $class_path = str_replace('Trophies/', '', $class_path);

    $file = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Trophies', 'classes', $class_path . '.php'));
    if (file_exists($file)) {
        require $file;
    }
});

// Initialise module
require_once(ROOT_PATH . '/modules/Trophies/module.php');
$module = new Trophies_Module($language, $trophies_language, $pages);