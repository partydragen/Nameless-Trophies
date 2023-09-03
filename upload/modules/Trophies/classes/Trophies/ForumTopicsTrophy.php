<?php

class ForumTopicsTrophy extends TrophyBase {

    public function __construct(){
        EventHandler::registerListener(TopicCreatedEvent::class, Trophies\Listeners\UserCreatedForumTopicListener::class);
    }

    public function getModule(): string {
        return 'Forum';
    }

    public function name(): string {
        return 'forumTopics';
    }

    public function description(): string {
        return 'User creates X amount of forum topics';
    }

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {
        $fields->add('score', Fields::NUMBER, 'Forum Topics Count', true, $trophy->exists() ? $trophy->data()->score : 0);
    }
}