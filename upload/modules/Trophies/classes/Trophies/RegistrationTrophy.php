<?php

class RegistrationTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(UserRegisteredEvent::class, Trophies\Listeners\UserRegisteredListener::class);
    }

    public function getModule(): string {
        return 'Core';
    }

    public function name(): string {
        return 'registration';
    }

    public function description(): string {
        return 'User registration trophy';
    }
}