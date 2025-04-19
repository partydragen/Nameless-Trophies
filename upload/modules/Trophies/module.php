<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *
 *  Trophies module initialisation file
 */

use Trophies\Widgets\LatestAwardedTrophiesWidget;
use Trophies\Widgets\UserTrophiesProfileWidget;

class Trophies_Module extends Module {

    private Language $_language;
    private Language $_trophies_language;

    public function __construct(Language $language, Language $trophies_language, Pages $pages, Cache $cache, User $user){
        $this->_language = $language;
        $this->_trophies_language = $trophies_language;

        $name = 'Trophies';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a> and my <a href="https://partydragen.com/supporters/" target="_blank">Sponsors</a>';
        $module_version = '1.1.3';
        $nameless_version = '2.1.2';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('Trophies', '/panel/trophies', 'pages/panel/trophies.php');

        // Register Events
        EventHandler::registerEvent(Trophies\Events\UserTrophyReceivedEvent::class);
        EventHandler::registerListener(UserDeletedEvent::class, Trophies\Listeners\UserDeletedListener::class);

        // Register Core Trophies
        Trophies::getInstance()->registerTrophy(new RegistrationTrophy());
        Trophies::getInstance()->registerTrophy(new ValidationTrophy());
        Trophies::getInstance()->registerTrophy(new LinkedIntegrationTrophy());
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

        // Members module integration
        if (Util::isModuleEnabled('Members')) {
            MemberListManager::getInstance()->registerListProvider(new MostTrophiesMemberListProvider($trophies_language));

            MemberListManager::getInstance()->registerMemberMetadataProvider(function (User $member) use ($trophies_language) {
                return [
                    $trophies_language->get('general', 'trophies') =>
                        DB::getInstance()->query(
                            'SELECT COUNT(user_id) AS `count` FROM nl2_users_trophies WHERE user_id = ?',
                            [$member->data()->id]
                        )->first()->count,
                ];
            });
        }

        // Check if module version changed
        $cache->setCache('trophies_module_cache');
        if (!$cache->isCached('module_version')) {
            $cache->store('module_version', $module_version);
        } else {
            if ($module_version != $cache->retrieve('module_version')) {
                // Version have changed, Perform actions
                $this->initialiseUpdate($cache->retrieve('module_version'));

                $cache->store('module_version', $module_version);

                if ($cache->isCached('update_check')) {
                    $cache->erase('update_check');
                }
            }
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
        // Register trophy after due of installation and reward issue
        Trophies::getInstance()->registerTrophy(new AccountAgeTrophy($user));

        $widgets->add(new LatestAwardedTrophiesWidget($template->getEngine(), $this->_language, $this->_trophies_language, $cache));
        $widgets->add(new UserTrophiesProfileWidget($template->getEngine(), $this->_trophies_language));

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

        // Check for module updates
        if (isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')) {
            // Page belong to this module?
            $page = $pages->getActivePage();
            if ($page['module'] == 'Trophies') {

                $cache->setCache('Trophies_module_cache');
                if ($cache->isCached('update_check')) {
                    $update_check = $cache->retrieve('update_check');
                } else {
                    $update_check = Trophies_Module::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {
                    $template->getEngine()->addVariables([
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_trophies_language->get('general', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->_trophies_language->get('general', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->_trophies_language->get('general', 'current_version_x', [
                            'version' => Output::getClean($this->getVersion())
                        ]),
                        'NEW_VERSION' => $this->_trophies_language->get('general', 'new_version_x', [
                            'new_version' => Output::getClean($update_check->new_version)
                        ]),
                        'NAMELESS_UPDATE' => $this->_trophies_language->get('general', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ]);
                }
            }
        }
    }

    public function getDebugInfo(): array {
        return [];
    }

    private function initialiseUpdate($old_version) {
        $old_version = str_replace([".", "-"], "", $old_version);

        if ($old_version < 113) {
            try {
                DB::getInstance()->query('ALTER TABLE `nl2_users_trophies` ADD INDEX `user_id` (`user_id`)');
            } catch (Exception $e) {
                // Error
            }
        }
    }

    private function initialise() {
        // Generate tables
        if (!DB::getInstance()->showTables('trophies')) {
            try {
                DB::getInstance()->createTable("trophies", " `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(64) NOT NULL, `description` varchar(64) NOT NULL, `score` int(11) NOT NULL, `type` varchar(64) NOT NULL, `parent` int(11) NOT NULL, `image` varchar(128) DEFAULT NULL, `data` text DEFAULT NULL, `reward_groups` varchar(128) DEFAULT NULL, `reward_credits_cents` int(11) NOT NULL DEFAULT '0', `enabled` int(11) NOT NULL DEFAULT '1', `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)");
            } catch (Exception $e) {
                // Error
            }
        }

        if (!DB::getInstance()->showTables('users_trophies')) {
            try {
                DB::getInstance()->createTable("users_trophies", " `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL, `trophy_id` int(11) NOT NULL, `received` int(11) NOT NULL, PRIMARY KEY (`id`)");

                DB::getInstance()->query('ALTER TABLE `nl2_users_trophies` ADD INDEX `user_id` (`user_id`)');
            } catch (Exception $e) {
                // Error
            }
        }
    }

    /*
     *  Check for Module updates
     *  Returns JSON object with information about any updates
     */
    private static function updateCheck() {
        $current_version = Settings::get('nameless_version');
        $uid = Settings::get('unique_id');

        $enabled_modules = Module::getModules();
        foreach ($enabled_modules as $enabled_item) {
            if ($enabled_item->getName() == 'Trophies') {
                $module = $enabled_item;
                break;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=Trophies&module_version='.$module->getVersion() . '&domain='. URL::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($update_check);
        if (isset($info->message)) {
            die($info->message);
        }

        return $update_check;
    }
}