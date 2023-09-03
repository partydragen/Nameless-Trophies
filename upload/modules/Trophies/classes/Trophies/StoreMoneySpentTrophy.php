<?php

class StoreMoneySpentTrophy extends TrophyBase {

    public function __construct(){

    }

    public function getModule(): string {
        return 'Store';
    }

    public function name(): string {
        return 'storeMoneySpent';
    }

    public function description(): string {
        return 'User spent X money on the Store';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Money spent on the Store', true, $trophy->exists() ? $trophy->data()->score : 0);
    }
}