<?php

class LinkedIntegrationTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(UserIntegrationVerifiedEvent::class, Trophies\Listeners\UserLinkedIntegrationListener::class);
    }

    public function getModule(): string {
        return 'Core';
    }

    public function name(): string {
        return 'linkedIntegration';
    }

    public function description(): string {
        return 'User links certain integration';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        if (Input::exists() && $trophy->exists()) {
            if (Token::check(Input::get('token'))) {
                DB::getInstance()->update('trophies', $trophy->data()->id, [
                    'data' => json_encode([
                        'integration' => Input::get('integration')
                    ]),
                ]);
            }
        }

        $data = $trophy->exists() ? json_decode($trophy->data()->data, true) : null;

        $fields->add('integration', Fields::SELECT, 'Integration', true, $data['integration'] ?? null);

        $integrations = Integrations::getInstance()->getAll();
        foreach ($integrations as $integration) {
            $fields->addOption('integration', $integration->getName(), $integration->getName());
        }
    }
}