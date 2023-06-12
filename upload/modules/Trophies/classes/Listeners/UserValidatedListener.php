<?php

namespace Trophies\Listeners;

use UserValidatedEvent;
use UserTrophies;

class UserValidatedListener {
    public static function execute(UserValidatedEvent $event): void {
        $user_trophies = new UserTrophies($event->user);
        $user_trophies->checkTrophyStatus('validation', 1);
    }
}