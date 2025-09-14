<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com/
 *  https://github.com/partydragen/Nameless-Trophies/
 *  NamelessMC version 2.2.0
 *
 *  License: MIT
 *
 *  Panel user trophies page
 */

if (!$user->handlePanelPageLoad('staffcp.trophies')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

if (!isset($_GET['user']) || !is_numeric($_GET['user'])) {
    Redirect::to(URL::build('/panel/users'));
}

$view_user = new User($_GET['user']);
if (!$view_user->exists()) {
    Redirect::to('/panel/users');
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'users');
define('PANEL_PAGE', 'users');
$page_title = $trophies_language->get('general', 'trophies');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (!isset($_GET['action'])) {
    if (Input::exists()) {
        $errors = [];

        if (Token::check(Input::get('token'))) {
            $validation = Validate::check($_POST, [
                'trophy' => [
                    Validate::REQUIRED => true,
                    Validate::NUMERIC => true
                ]
            ]);

            if ($validation->passed()) {
                $trophy = new Trophy($_POST['trophy']);
                if ($trophy->exists()) {
                    $user_trophies = new UserTrophies($view_user);
                    if (!$user_trophies->hasTrophy($trophy)) {
                        $user_trophies->rewardTrophy($trophy);

                        Session::flash('trophies_success', 'Successfully rewarded the trophy to ' . $view_user->getDisplayname(true));
                        Redirect::to(URL::build('/panel/users/trophies', 'user=' . $view_user->data()->id));
                    } else {
                        $errors[] = 'User already have this trophy!';
                    }
                } else {
                    $errors[] = 'Trophy not found';
                }
            } else {
                // Errors
                $errors = $validation->errors();
            }
        } else {
            $errors[] = $language->get('general', 'invalid_token');
        }
    }

    // Get user trophies
    $trophies_list = [];
    $user_trophies = new UserTrophies($view_user);
    foreach ($user_trophies->getTrophies() as $trophy) {
        $trophies_list[] = [
            'rid' => $trophy->data()->reward_id,
            'title' => Output::getClean($trophy->data()->title),
            'description' => Output::getClean($trophy->data()->description),
            'image' => $trophy->getImage(),
            'received_date' => date(DATE_FORMAT, $trophy->data()->received),
            'awarded_date' => $trophies_language->get('general', 'awarded_date', [
                'date' => date(DATE_FORMAT, $trophy->data()->received)
            ])
        ];
    }

    // View trophies
    $all_trophies_list = [];
    $trophies_query = DB::getInstance()->query('SELECT * FROM nl2_trophies');
    foreach ($trophies_query->results() as $item) {
        $trophy = new Trophy(null, null, $item);

        $all_trophies_list[] = [
            'id' => $trophy->data()->id,
            'title' => Output::getClean($trophy->data()->title)
        ];
    }

    $template->getEngine()->addVariables([
        'TROPHIES_LIST' => $trophies_list,
        'ALL_TROPHIES_LIST' => $all_trophies_list,
        'NONE_TROPHIES' => $trophies_language->get('general', 'user_no_trophies'),
        'REMOVE' => $language->get('general', 'remove'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_REMOVE_TROPHY' => $trophies_language->get('admin', 'confirm_remove_trophy_from_user'),
        'DELETE_LINK' => URL::build('/panel/users/trophies/', 'user=' . $view_user->data()->id . '&action=delete'),
        'TOKEN' => Token::get(),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no')
    ]);
} else {
    if ($_GET['action'] == 'delete') {
        // Delete trophy from user
        if (Input::exists()) {
            if (Token::check(Input::get('token'))) {
                if (isset($_POST['id'])) {
                    DB::getInstance()->delete('users_trophies', ['id', '=', $_POST['id']]);

                    Session::flash('trophies_success', $trophies_language->get('admin', 'user_trophy_deleted_successfully'));
                }
            } else {
                Session::flash('trophies_error', $language->get('general', 'invalid_token'));
            }
        }
        die();
    }

    Redirect::to(URL::build('/panel/trophies'));
}

if (Session::exists('trophies_success'))
    $success = Session::flash('trophies_success');

if (isset($success))
    $template->getEngine()->addVariables([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $template->getEngine()->addVariables([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

$template->getEngine()->addVariables([
    'PARENT_PAGE' => PARENT_PAGE,
    'PAGE' => PANEL_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'USER_MANAGEMENT' => $language->get('admin', 'user_management'),
    'TROPHIES' => $trophies_language->get('general', 'trophies'),
    'VIEWING_USER' => $language->get('moderator', 'viewing_user_x', ['user' => $view_user->getDisplayname()]),
    'BACK_LINK' => URL::build('/panel/user/' . $view_user->data()->id),
    'BACK' => $language->get('general', 'back'),
    'TOKEN' => Token::get(),
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('trophies/users_trophies');