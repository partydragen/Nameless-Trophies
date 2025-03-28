<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com/resources/resource/5-store-module/
 *  https://partydragen.com/
 *
 *  License: MIT
 *
 *  Store module - Latest purchases widget
 */

namespace Trophies\Widgets;

use Cache;
use DB;
use Language;
use Output;
use TemplateEngine;
use TimeAgo;
use Trophy;
use User;
use WidgetBase;

class LatestAwardedTrophiesWidget extends WidgetBase {
    private Cache $_cache;
    private Language $_language;
    private Language $_trophies_language;

    public function __construct(TemplateEngine $engine, Language $language, Language $trophies_language, Cache $cache) {
        $this->_module = 'Trophies';
        $this->_name = 'Latest Trophies';
        $this->_description = 'Displays a list of latest awarded trophies';
        $this->_engine = $engine;
        $this->_language = $language;
        $this->_trophies_language = $trophies_language;
        $this->_cache = $cache;
    }

    public function initialise(): void {
        // Generate HTML code for widget
        $timeago = new TimeAgo(TIMEZONE);

        $rewarded_trophies = [];
        $rewarded_trophies_query = DB::getInstance()->query('SELECT trophy_id, user_id, received, title, description FROM `nl2_users_trophies` INNER JOIN `nl2_trophies` ON trophy_id=nl2_trophies.id ORDER BY received LIMIT 10');
        foreach ($rewarded_trophies_query->results() as $item) {
            $trophy = new Trophy(null, null, $item);

            $target_user = new User($item->user_id);
            if (!$target_user->exists()) {
                continue;
            }

            $rewarded_trophies[] = [
                'trophy_title' => Output::getClean($item->title),
                'trophy_description' => Output::getClean($item->description),
                'trophy_image' => $trophy->getImage(),
                'trophy_received_friendly' => $timeago->inWords($item->received, $this->_language),
                'trophy_received_full' => date(DATE_FORMAT, $item->received),
                'username' => $target_user->getDisplayname(),
                'user_id' => $target_user->data()->id,
                'user_style' => $target_user->getGroupStyle(),
                'user_avatar' => $target_user->getAvatar(),
                'user_profile' => $target_user->getProfileURL(),
            ];
        }

        $this->_engine->addVariables([
            'LATEST_REWARDED_TROPHIES' => $this->_trophies_language->get('general', 'latest_rewarded_trophies'),
            'NO_TROPHIES_REWARDED' => $this->_trophies_language->get('general', 'no_trophies_rewarded'),
            'LATEST_REWARDED_TROPHIES_LIST' => $rewarded_trophies
        ]);

        $this->_content = $this->_engine->fetch('trophies/widgets/latest_awarded_trophies');
    }
}