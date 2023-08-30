<?php

namespace Trophies\Listeners;

use TopicCreatedEvent;
use UserTrophies;
use Forum;

class UserCreatedForumTopicListener {
    public static function execute(TopicCreatedEvent $event): void {
        $forum = new Forum();

        $user_trophies = new UserTrophies($event->creator);
        $user_trophies->checkTrophyStatus('forumTopics', $forum->getTopicCount($event->creator->data()->id));
        $user_trophies->checkTrophyStatus('forumPosts', $forum->getPostCount($event->creator->data()->id));
    }
}