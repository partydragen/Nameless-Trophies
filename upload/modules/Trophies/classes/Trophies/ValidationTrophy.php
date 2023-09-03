<?php

class ValidationTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(UserValidatedEvent::class, Trophies\Listeners\UserValidatedListener::class);
    }

    public function getModule(): string {
        return 'Core';
    }

    public function name(): string {
        return 'validation';
    }

    public function description(): string {
        return 'User validates their email';
    }
}