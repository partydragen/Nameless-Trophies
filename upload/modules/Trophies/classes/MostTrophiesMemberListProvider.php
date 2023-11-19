<?php

/**
 * Most trophies member list provider
 *
 * @package Modules\Trophies
 * @author Partydragen
 * @version 2.1.0
 * @license MIT
 */
class MostTrophiesMemberListProvider extends MemberListProvider {

    public function __construct(Language $language) {
        $this->_name = 'most_trophies';
        $this->_friendly_name = $language->get('general', 'most_trophies');
        $this->_module = 'Trophies';
        $this->_icon = 'trophy icon';
    }

    protected function generator(): array {
        return [
            'SELECT user_id, COUNT(user_id) AS `count` FROM nl2_users_trophies GROUP BY user_id ORDER BY `count` DESC',
            'user_id',
            'count'
        ];
    }
}