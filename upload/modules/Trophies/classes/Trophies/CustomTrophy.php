<?php

class CustomTrophy extends TrophyBase {

    public function getModule(): string {
        return 'Trophies';
    }

    public function name(): string {
        return 'custom';
    }

    public function description(): string {
        return 'Custom trophy (Can only be given manually)';
    }
}