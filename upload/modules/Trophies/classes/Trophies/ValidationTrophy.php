<?php

class ValidationTrophy extends TrophyBase {

    public function name(): string {
        return 'validation';
    }

    public function description(): string {
        return 'User validates their email';
    }
}