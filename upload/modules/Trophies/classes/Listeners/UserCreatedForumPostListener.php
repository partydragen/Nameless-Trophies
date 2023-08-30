<?php

namespace Trophies\Listeners;

use UserTrophies;
use Forum;

class UserCreatedForumPostListener {
    public static function execute(array $params = []): void {
        $forum = new Forum();

        $user_trophies = new UserTrophies($params['user']);
        $user_trophies->checkTrophyStatus('forumPosts', $forum->getPostCount($params['user']->data()->id));
    }
}