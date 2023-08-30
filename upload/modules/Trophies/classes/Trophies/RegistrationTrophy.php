<?php

class RegistrationTrophy extends TrophyBase {

    public function name(): string {
        return 'registration';
    }

    public function description(): string {
        return 'User registration trophy';
    }
}