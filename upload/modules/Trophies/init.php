<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *
 *  Trophies module initialisation file
 */

// Initialise trophies language
$trophies_language = new Language(ROOT_PATH . '/modules/Trophies/language', LANGUAGE);

require_once(ROOT_PATH . '/modules/Trophies/autoload.php');

// Initialise module
require_once(ROOT_PATH . '/modules/Trophies/module.php');
$module = new Trophies_Module($language, $trophies_language, $pages, $cache, $user);

// Profile page tab
try {
    if (!isset($profile_tabs)) $profile_tabs = array();
    $profile_tabs['trophies'] = array('title' => $trophies_language->get('general', 'trophies'), 'smarty_template' => 'trophies/profile_tab.tpl', 'require' => ROOT_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Trophies' . DIRECTORY_SEPARATOR . 'profile_tab.php');
} catch(Exception $e){
    // Error
}