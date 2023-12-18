<?php
/*
 *  Made by Partydragen
 *  https://partydragen.com/
 *  https://github.com/partydragen/Nameless-Trophies/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 *
 *  Panel trophies page
 */

if (!$user->handlePanelPageLoad('staffcp.trophies')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

const PAGE = 'panel';
const PARENT_PAGE = 'trophies';
const PANEL_PAGE = 'trophies';
$page_title = $trophies_language->get('general', 'trophies');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (!isset($_GET['action']) && !isset($_GET['trophy'])) {
    if (Input::exists()) {
        $errors = [];

        if (Token::check(Input::get('token'))) {
            $validation = Validate::check($_POST, [
                'username' => [
                    Validate::REQUIRED => true
                ],
                'trophy' => [
                    Validate::REQUIRED => true,
                    Validate::NUMERIC => true
                ]
            ]);

            if ($validation->passed()) {
                $target_user = new User($_POST['username'], 'username');
                if ($target_user->exists()) {
                    $trophy = new Trophy($_POST['trophy']);
                    if ($trophy->exists()) {
                        $user_trophies = new UserTrophies($target_user);
                        if (!$user_trophies->hasTrophy($trophy)) {
                            $user_trophies->rewardTrophy($trophy);

                            Session::flash('trophies_success', 'Successfully rewarded the trophy to ' . $target_user->getDisplayname(true));
                            Redirect::to(URL::build('/panel/trophies'));
                        } else {
                            $errors[] = 'User already have this trophy!';
                        }
                    } else {
                        $errors[] = 'Trophy not found';
                    }
                } else {
                    $errors[] = 'User not found';
                }
            } else {
                // Errors
                $errors = $validation->errors();
            }
        } else {
            $errors[] = $language->get('general', 'invalid_token');
        }
    }

    // View trophies
    $trophies = [];
    $trophies_query = DB::getInstance()->query('SELECT * FROM nl2_trophies');
    foreach ($trophies_query->results() as $item) {
        $trophy = new Trophy(null, null, $item);

        $trophies[] = [
            'id' => $trophy->data()->id,
            'title' => Output::getClean($trophy->data()->title),
            'description' => Output::getClean($trophy->data()->description),
            'score' => Output::getClean($trophy->data()->score),
            'type' => Output::getClean($trophy->data()->type),
            'image' => $trophy->getImage(),
            'edit_link' => URL::build('/panel/trophies/', 'trophy=' . $trophy->data()->id)
        ];
    }

    $smarty->assign([
        'TROPHIES_LIST' => $trophies,
        'DELETE_LINK' => URL::build('/panel/trophies/', 'action=delete'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_TROPHY' => $trophies_language->get('admin', 'confirm_delete_trophy'),
        'TOKEN' => Token::get(),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no')
    ]);

    $template_file = 'trophies/trophies.tpl';
} else {
    if ($_GET['action'] == 'new') {
        if (!isset($_GET['type'])) {
            // Select trophy type
            $trophies_type_list = [];
            foreach (Trophies::getInstance()->getAll() as $trophy) {
                $trophies_type_list[] = [
                    'name' => Output::getClean($trophy->name()),
                    'enabled' => Output::getClean($trophy->enabled()),
                    'module' => Output::getClean($trophy->getModule()),
                    'description' => Output::getClean($trophy->description()),
                    'select_link' => URL::build('/panel/trophies/' , 'action=new&type=' . $trophy->name()),
                ];
            }

            $smarty->assign([
                'TROPHY_TITLE' => 'Select Trophy Type',
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/trophies'),
                'TROPHIES_TYPE_LIST' => $trophies_type_list
            ]);

            $template_file = 'trophies/trophies_new_step_1.tpl';
        } else {
            // Create new trophy
            $trophy_type = Trophies::getInstance()->getTrophy($_GET['type']);
            if ($trophy_type == null) {
                Redirect::to(URL::build('/panel/trophies'));
            }

            $trophy = new Trophy();
            $fields = new Fields();

            $validation = null;
            if (Input::exists()) {
                $errors = [];

                if (Token::check(Input::get('token'))) {
                    $validation = Validate::check($_POST, [
                        'title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 64
                        ],
                        'description' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 2048
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Save to database
                        DB::getInstance()->insert('trophies', [
                            'title' => Input::get('title'),
                            'description' => Input::get('description'),
                            'score' => $_POST['score'] ?? 1,
                            'type' => $trophy_type->name(),
                            'parent' => 0,
                            'reward_groups' => json_encode(isset($_POST['add_groups']) && is_array($_POST['add_groups']) ? $_POST['add_groups'] : []),
                            'reward_credits_cents' => (int) (string) ((float) preg_replace("/[^0-9.]/", "", Input::get('add_credits')) * 100),
                            'enabled' => 1
                        ]);

                        $trophy = new Trophy(DB::getInstance()->lastId());
                        $trophy_type->settingsPageLoad($fields, $template, $trophy, $validation);

                        Session::flash('trophies_success', $trophies_language->get('admin', 'trophy_created_successfully'));
                        Redirect::to(URL::build('/panel/trophies/', 'trophy=' . $trophy->data()->id));
                    } else {
                        // Errors
                        $errors = $validation->errors();
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $title_value = ((isset($_POST['title']) && $_POST['title']) ? Output::getClean(Input::get('title')) : '');
            $description_value = ((isset($_POST['description']) && $_POST['description']) ? Output::getClean(Input::get('description')) : '');

            $fields->add('title', Fields::TEXT, 'Trophy Title', true, $title_value);
            $fields->add('description', Fields::TEXT, 'Trophy Description', true, $description_value);

            $trophy_type->settingsPageLoad($fields, $template, $trophy, $validation);

            $smarty->assign([
                'TROPHY_TITLE' => 'Creating new trophy',
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/trophies'),
                'FIELDS' => $fields->getAll(),
                'ALL_GROUPS' => DB::getInstance()->orderAll('groups', '`order`', 'ASC')->results(),
                'ADD_GROUPS_VALUE' => ((isset($_POST['add_groups']) && is_array($_POST['add_groups'])) ? $_POST['add_groups'] : []),
                'ADD_CREDITS_VALUE' => ((isset($_POST['add_credits']) && $_POST['add_credits']) ? Output::getClean($_POST['add_credits']) : '0.00'),
            ]);

            $template->addJSScript('
                $(document).ready(() => {
                    $(\'#inputAddGroups\').select2({ placeholder: "No groups selected" });
                })
            ');

            $template_file = 'trophies/trophies_new_step_2.tpl';
        }
    } else if (isset($_GET['trophy'])) {
        // Edit existing trophy
        $trophy = new Trophy($_GET['trophy']);
        if (!$trophy->exists()) {
            Redirect::to(URL::build('/panel/trophies'));
        }

        $trophy_type = Trophies::getInstance()->getTrophy($trophy->data()->type);
        if ($trophy_type == null) {
            Redirect::to(URL::build('/panel/trophies'));
        }

        $fields = new Fields();

        $validation = null;
        if (Input::exists()) {
            $errors = [];

            if (Token::check(Input::get('token'))) {
                if (Input::get('type') == 'settings') {
                    $validation = Validate::check($_POST, [
                        'title' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 64
                        ],
                        'description' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 2048
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Save to database
                        DB::getInstance()->update('trophies', $trophy->data()->id, [
                            'title' => Input::get('title'),
                            'description' => Input::get('description'),
                            'score' => $_POST['score'] ?? 1,
                            'parent' => 0,
                            'reward_groups' => json_encode(isset($_POST['add_groups']) && is_array($_POST['add_groups']) ? $_POST['add_groups'] : []),
                            'reward_credits_cents' => (int)(string)((float)preg_replace("/[^0-9.]/", "", Input::get('add_credits')) * 100),
                            'enabled' => 1
                        ]);

                        $trophy_type->settingsPageLoad($fields, $template, $trophy, $validation);

                        Session::flash('trophies_success', $trophies_language->get('admin', 'trophy_updated_successfully'));
                        Redirect::to(URL::build('/panel/trophies'));
                    } else {
                        // Errors
                        $errors = $validation->errors();
                    }
                } else if (Input::get('type') == 'image') {
                    // Trophy image
                    if (!is_dir(ROOT_PATH . '/uploads/trophies')) {
                        try {
                            mkdir(ROOT_PATH . '/uploads/trophies');
                        } catch (Exception $e) {
                            $errors[] = $trophies_language->get('admin', 'unable_to_create_image_directory');
                        }
                    }

                    if (!count($errors)) {
                        $image = new Bulletproof\Image($_FILES);

                        $image->setSize(1000, 2 * 1048576)
                            ->setMime(['jpeg', 'png', 'gif'])
                            ->setDimension(200, 000)
                            ->setLocation(ROOT_PATH . '/uploads/trophies', 0777);

                        if ($image['trophy_image']) {
                            $upload = $image->upload();

                            if ($upload) {
                                DB::getInstance()->update('trophies', $trophy->data()->id, [
                                    'image' => $image->getName() . '.' . $image->getMime()
                                ]);

                                Session::flash('trophies_success', $trophies_language->get('admin', 'image_updated_successfully'));
                                Redirect::to(URL::build('/panel/trophies'));
                            } else {
                                $errors[] = $trophies_language->get('admin', 'unable_to_upload_image', ['error' => Output::getClean($image->getError())]);
                            }
                        }
                    }
                }
            } else {
                $errors[] = $language->get('general', 'invalid_token');
            }
        }

        $fields->add('title', Fields::TEXT, 'Trophy Title', true, Output::getClean($trophy->data()->title));
        $fields->add('description', Fields::TEXT, 'Trophy Description', true, Output::getClean($trophy->data()->description));

        $trophy_type->settingsPageLoad($fields, $template, $trophy, $validation);

        $smarty->assign([
            'TROPHY_TITLE' => 'Editing trophy',
            'BACK' => $language->get('general', 'back'),
            'BACK_LINK' => URL::build('/panel/trophies'),
            'FIELDS' => $fields->getAll(),
            'IMAGE_VALUE' => $trophy->getImage(),
            'UPLOAD_NEW_IMAGE' => $trophies_language->get('admin', 'upload_new_image'),
            'BROWSE' => $language->get('general', 'browse'),
            'ALL_GROUPS' => DB::getInstance()->orderAll('groups', '`order`', 'ASC')->results(),
            'ADD_GROUPS_VALUE' => json_decode($trophy->data()->reward_groups, true) ?? [],
            'ADD_CREDITS_VALUE' => Output::getClean(sprintf('%0.2f', $trophy->data()->reward_credits_cents / 100))
        ]);

        $template->addJSScript('
            $(document).ready(() => {
                $(\'#inputAddGroups\').select2({ placeholder: "No groups selected" });
            })
        ');

        $template_file = 'trophies/trophies_edit.tpl';
    } else if ($_GET['action'] == 'delete') {
        // Delete trophy
        if (Input::exists()) {
            if (Token::check(Input::get('token'))) {
                if (isset($_POST['id'])) {
                    DB::getInstance()->delete('trophies', ['id', '=', $_POST['id']]);

                    Session::flash('trophies_success', $trophies_language->get('admin', 'trophy_deleted_successfully'));
                }
            } else {
                Session::flash('trophies_error', $language->get('general', 'invalid_token'));
            }
        }
        die();
    } else {
        Redirect::to(URL::build('/panel/trophies'));
    }
}

if (Session::exists('trophies_success')) {
    $success = Session::flash('trophies_success');
}

if (Session::exists('trophies_error')) {
    $errors[] = Session::flash('trophies_error');
}

if (isset($success)) {
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);
}

if (isset($errors) && count($errors)) {
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);
}

$smarty->assign([
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'PARENT_PAGE' => PARENT_PAGE,
    'PAGE' => PANEL_PAGE,
    'TROPHIES' => $trophies_language->get('general', 'trophies'),
    'NEW_TROPHY' => $trophies_language->get('general', 'new_trophy'),
    'NEW_TROPHY_LINK' => URL::build('/panel/trophies/', 'action=new'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);