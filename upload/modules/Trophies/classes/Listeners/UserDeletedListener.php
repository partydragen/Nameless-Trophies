<?php

namespace Trophies\Listeners;

use DB;
use UserDeletedEvent;

class UserDeletedListener {
    public static function execute(UserDeletedEvent $event): void {
        DB::getInstance()->query('DELETE FROM nl2_users_trophies WHERE user_id = ?', [$event->user->data()->id]);
    }
}