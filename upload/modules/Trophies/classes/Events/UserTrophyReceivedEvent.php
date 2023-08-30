<?php

namespace Trophies\Events;

use Trophy;
use AbstractEvent;
use User;
use Language;
use HasWebhookParams;
use DiscordDispatchable;
use DiscordWebhookBuilder;
use DiscordEmbed;

class UserTrophyReceivedEvent extends AbstractEvent implements HasWebhookParams, DiscordDispatchable {
    public User $user;
    public Trophy $trophy;

    public function __construct(User $user, Trophy $trophy) {
        $this->user = $user;
        $this->trophy = $trophy;
    }

    public static function name(): string {
        return 'userTrophyReceived';
    }

    public static function description(): string {
        return (new Language(ROOT_PATH . '/modules/Trophies/language'))->get('general', 'user_trophy_received');
    }

    public function webhookParams(): array {
        return [
            'user' => [
                'id' => $this->user->data()->id,
                'username' => $this->user->data()->username,
            ],
            'trophy_id' => $this->trophy->data()->id,
            'trophy_name' => $this->trophy->data()->title
        ];
    }

    public function toDiscordWebhook(): DiscordWebhookBuilder {
        $language = new Language('core', DEFAULT_LANGUAGE);

        return DiscordWebhookBuilder::make()
            ->setUsername($this->user->getDisplayname() . ' | ' . SITE_NAME)
            ->setAvatarUrl($this->user->getAvatar(128, true))
            ->addEmbed(function (DiscordEmbed $embed) use ($language) {
                return $embed
                    ->setTitle($this->user->getDisplayname() . ' has been rewarded the trophy ' . $this->trophy->data()->title)
                    ->setDescription($this->trophy->data()->description . "\n\nRewarded: ");
            });
    }
}