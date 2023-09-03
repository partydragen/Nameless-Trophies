<?php

class StorePurchasesTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(PaymentCompletedEvent::class, Trophies\Listeners\StorePaymentListener::class);
    }

    public function getModule(): string {
        return 'Store';
    }

    public function name(): string {
        return 'storePurchases';
    }

    public function description(): string {
        return 'User made X purchases on the Store';
    }


    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Amount of store purchases', true, $trophy->exists() ? $trophy->data()->score : 0);
    }
}