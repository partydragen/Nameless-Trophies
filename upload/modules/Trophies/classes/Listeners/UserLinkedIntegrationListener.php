<?php

namespace Trophies\Listeners;

use Trophies;
use UserIntegrationVerifiedEvent;
use UserTrophies;

class UserLinkedIntegrationListener {
    public static function execute(UserIntegrationVerifiedEvent $event): void {
        $trophies = Trophies::getInstance()->getTrophies();
        $user_trophies = new UserTrophies($event->user);

        if (array_key_exists('linkedIntegration', $trophies)) {
            foreach ($trophies['linkedIntegration'] as $trophy) {
                $data = json_decode($trophy->data()->data, true) ?? [];

                if (isset($data['integration']) && $data['integration'] == $event->integration->getName()) {
                    if (!$user_trophies->hasTrophy($trophy)) {
                        $user_trophies->rewardTrophy($trophy);
                    }
                }
            }
        }
    }
}