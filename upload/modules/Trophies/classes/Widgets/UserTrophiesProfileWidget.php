<?php

namespace Trophies\Widgets;

use Language;
use Output;
use ProfileWidgetBase;
use TemplateEngine;
use User;
use UserTrophies;

class UserTrophiesProfileWidget extends ProfileWidgetBase {

    private Language $_trophies_language;

    public function __construct(TemplateEngine $engine, Language $trophies_language) {
        $this->_name = 'User Trophies';
        $this->_description = 'Display the trophies the user has earned';
        $this->_module = 'Trophies';

        $this->_engine = $engine;
        $this->_trophies_language = $trophies_language;
    }

    public function initialise(User $user): void {
        /*$trophies = [];

        $user_trophies = new UserTrophies($user);
        foreach ($user_trophies->getTrophies() as $trophy) {
            $trophies[] = [
                'title' => Output::getClean($trophy->data()->title),
                'description' => Output::getClean($trophy->data()->description),
                'image' => $trophy->getImage(),
                'received_date' => date(DATE_FORMAT, $trophy->data()->received),
                'awarded_date' => $this->_trophies_language->get('general', 'awarded_date', [
                    'date' => date(DATE_FORMAT, $trophy->data()->received)
                ])
            ];
        }

        // Smarty
        $this->_engine->addVariables([
            'TROPHIES' => $trophies,
            'NONE_TROPHIES' => $this->_trophies_language->get('general', 'user_no_trophies')
        ]);*/

        $this->_content = $this->_engine->fetch('trophies/widgets/user_trophies');
    }
}