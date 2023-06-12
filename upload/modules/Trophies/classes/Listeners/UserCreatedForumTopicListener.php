<?php

namespace Trophies\Listeners;

use TopicCreatedEvent;
use UserTrophies;
use Forum;

class UserCreatedForumTopicListener {
    public static function execute(TopicCreatedEvent $event): void {
        $forum = new Forum();

        $user_trophies = new UserTrophies($event->creator);
        $user_trophies->checkTrophyStatus('forum_topic', $forum->getTopicCount($event->creator->data()->id));

        $user_trophies = new UserTrophies($event->creator);
        $user_trophies->checkTrophyStatus('forum_post', $forum->getTopicCount($event->creator->data()->id));
    }
}