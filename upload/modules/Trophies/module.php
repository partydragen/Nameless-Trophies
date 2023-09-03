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

    public function __construct(Language $language, Language $trophies_language, Pages $pages, User $user){
        $this->_language = $language;
        $this->_trophies_language = $trophies_language;

        $name = 'Trophies';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.0.0';
        $nameless_version = '2.1.0';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('Trophies', '/panel/trophies', 'pages/panel/trophies.php');

        // Register Events
        EventHandler::registerEvent(Trophies\Events\UserTrophyReceivedEvent::class);

        // Register Core Trophies
        Trophies::getInstance()->registerTrophy(new RegistrationTrophy());
        Trophies::getInstance()->registerTrophy(new ValidationTrophy());
        Trophies::getInstance()->registerTrophy(new LinkedIntegrationTrophy());
        Trophies::getInstance()->registerTrophy(new AccountAgeTrophy($user));
        Trophies::getInstance()->registerTrophy(new CustomTrophy());

        // Register Forum Trophies and listeners if module is enabled
        if (Util::isModuleEnabled('Forum')) {
            Trophies::getInstance()->registerTrophy(new ForumTopicsTrophy());
            Trophies::getInstance()->registerTrophy(new ForumPostsTrophy());
        }

        // Register Store Trophies and listeners if module is enabled
        if (Util::isModuleEnabled('Store')) {
            Trophies::getInstance()->registerTrophy(new StorePurchasesTrophy());
            Trophies::getInstance()->registerTrophy(new StoreMoneySpentTrophy());
        }

        // Register Referrals Trophies and listeners if module is enabled
        if (Util::isModuleEnabled('Referrals')) {
            Trophies::getInstance()->registerTrophy(new ReferralRegistrationsTrophy());
        }
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
        if (defined('BACK_END')) {
            if ($user->hasPermission('admincp.trophies')) {
                $cache->setCache('panel_sidebar');
                if (!$cache->isCached('trophies_order')) {
                    $order = 98;
                    $cache->store('trophies_order', 98);
                } else {
                    $order = $cache->retrieve('trophies_order');
                }
                $navs[2]->add('trophies_divider', mb_strtoupper($this->_trophies_language->get('general', 'trophies'), 'UTF-8'), 'divider', 'top', null, $order, '');

                if (!$cache->isCached('trophies_icon')) {
                    $icon = '<i class="nav-icon fa-solid fa-link"></i>';
                    $cache->store('trophies_icon', $icon);
                } else {
                    $icon = $cache->retrieve('referrals_icon');
                }
                $navs[2]->add('trophies', $this->_trophies_language->get('general', 'trophies'), URL::build('/panel/trophies'), 'top', null, $order + 0.1, $icon);
            }
        }
    }

    public function getDebugInfo(): array {
        return [];
    }

    private function initialise() {

    }
}