<?php

class ReferralRegistrationsTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(Referrals\Events\ReferralRegistrationEvent::class, Trophies\Listeners\UserReferralRegistrationListener::class);
    }

    public function getModule(): string {
        return 'Referrals';
    }

    public function name(): string {
        return 'referralRegistrations';
    }

    public function description(): string {
        return 'User referred X users to register';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Referral Registrations', true, $trophy->exists() ? $trophy->data()->score : 0);
    }
}