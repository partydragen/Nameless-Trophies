<?php

class ForumPostsTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener('prePostCreate', 'Trophies\Listeners\UserCreatedForumPostListener::execute');
    }

    public function getModule(): string {
        return 'Forum';
    }

    public function name(): string {
        return 'forumPosts';
    }

    public function description(): string {
        return 'User creates X amount of forum posts';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Forum Posts Count', true, $trophy->exists() ? $trophy->data()->score : 0);
    }
}