<?php


class UserTrophyReceivedEvent extends AbstractEvent {
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
}