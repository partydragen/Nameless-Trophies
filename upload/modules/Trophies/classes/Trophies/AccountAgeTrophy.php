<?php

class AccountAgeTrophy extends TrophyBase {

    public function __construct(User $user) {
        if ($user->isLoggedIn()) {
            $user_trophies = new UserTrophies($user);
            $user_trophies->checkTrophyStatus('accountAge', $this->getAgeYears($user));
        }
    }

    public function getModule(): string {
        return 'Core';
    }

    public function name(): string {
        return 'accountAge';
    }

    public function description(): string {
        return 'Account age trophy';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Account age (Years)', true, $trophy->exists() ? $trophy->data()->score : 0);
    }

    public function getAgeYears(User $user): int {
        $accountCreated = date("Y-m-d", $user->data()->joined);
        $today = date("Y-m-d");
        $diff = date_diff(date_create($accountCreated), date_create($today));

        return $diff->y;
    }
}