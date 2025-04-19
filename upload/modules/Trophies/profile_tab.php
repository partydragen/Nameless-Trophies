<?php
$trophies = [];

$user_trophies = new UserTrophies($profile_user);
foreach ($user_trophies->getTrophies() as $trophy) {
    $trophies[] = [
        'title' => Output::getClean($trophy->data()->title),
        'description' => Output::getClean($trophy->data()->description),
        'image' => $trophy->getImage(),
        'received_date' => date(DATE_FORMAT, $trophy->data()->received),
        'awarded_date' => $trophies_language->get('general', 'awarded_date', [
            'date' => date(DATE_FORMAT, $trophy->data()->received)
        ])
    ];
}

// Smarty
$template->getEngine()->addVariables([
    'TROPHIES' => $trophies,
    'TROPHIES_TITLE' => $trophies_language->get('general', 'trophies'),
    'NONE_TROPHIES' => $trophies_language->get('general', 'user_no_trophies')
]);