<?php

namespace Trophies\Listeners;

use UserRegisteredEvent;
use UserTrophies;

class UserRegisteredListener {
    public static function execute(UserRegisteredEvent $event): void {
        $user_trophies = new UserTrophies($event->user);
        $user_trophies->checkTrophyStatus('registration', 1);
    }
}