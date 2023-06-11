<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *
 *  Trophies module initialisation file
 */

class Trophies_Module extends Module {

    private Language $_language;
    private Language $_trophies_language;

    public function __construct($language, $trophies_language, $pages){
        $this->_language = $language;
        $this->_trophies_language = $trophies_language;

        $name = 'Trophies';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.0.0';
        $nameless_version = '2.1.0';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('Trophies', '/panel/trophies', 'pages/panel/trophies.php');
    }

    public function onInstall() {
        // Initialise
        $this->initialise();
    }

    public function onUninstall() {
        // No actions necessary
    }

    public function onEnable() {
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable() {
        // No actions necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
        // No actions necessary

        $user_trophies = new UserTrophies($user);
        $user_trophies->checkTrophyStatus('forum_post', 15);
    }

    public function getDebugInfo(): array {
        return [];
    }

    private function initialise() {

    }
}