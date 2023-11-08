<?php

abstract class TrophyBase {

    /**
     * Get the module name of the trophy.
     *
     * @return string The module name of the trophy
     */
    abstract public function getModule(): string;

    /**
     * Get the name of the trophy.
     *
     * @return string The name of the trophy
     */
    abstract public function name(): string;

    /**
     * Get the description of the trophy.
     *
     * @return string The description of the trophy
     */
    abstract public function description(): string;

    public function settingsPageLoad(Fields $fields, TemplateBase $template, Trophy $trophy, ?Validate $validation): void {

    }

    public function enabled(): bool {
        return true;
    }
}