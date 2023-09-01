<?php

class ReferralRegistrationsTrophy extends TrophyBase {

    public function name(): string {
        return 'referralRegistrations';
    }

    public function description(): string {
        return 'User referred X users to register';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Referral Registrations', true, $trophy->data()->score);
    }
}