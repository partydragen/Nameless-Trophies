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
use Util;

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
        $trophies_language = new Language(ROOT_PATH . '/modules/Trophies/language', DEFAULT_LANGUAGE);

        return DiscordWebhookBuilder::make()
            ->setUsername($this->user->getDisplayname() . ' | ' . SITE_NAME)
            ->setAvatarUrl($this->user->getAvatar(128, true))
            ->addEmbed(function (DiscordEmbed $embed) use ($trophies_language) {
                return $embed
                    ->setTitle($trophies_language->get('general', 'user_received_trophy', [
                        'user' => $this->user->getDisplayname(),
                        'trophy' => $this->trophy->data()->title
                    ]))
                    ->setThumbnail($this->trophy->getImage(true))
                    ->setDescription($this->trophy->data()->description)
                    ->setFooter(($this->trophy->data()->reward_credits_cents > 0 && Util::isModuleEnabled('Store')) ? $trophies_language->get('general', 'rewarded_x_for_completion', [
                        'rewarded' => $this->fromCents($this->trophy->data()->reward_credits_cents) . ' Store Credits'
                    ]) : null);
            });
    }

    public static function fromCents(int $cents): string {
        return sprintf('%0.2f', $cents / 100);
    }
}