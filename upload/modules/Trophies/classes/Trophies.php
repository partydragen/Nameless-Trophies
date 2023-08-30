<?php
/**
 * Trophies class
 * https://partydragen.com
 *
 * @package NamelessMC\Trophies
 * @author Partydragen
 * @version 2.1.0
 * @license MIT
 */
class Trophies extends Instanceable {

    private array $_trophy_types = [];
    private array $_trophies;

    public function getTrophies(): array {
        return $this->_trophies ??= (function (): array {
            $this->_trophies = [];

            $trophies_query = DB::getInstance()->query('SELECT * FROM nl2_trophies');
            if ($trophies_query->count()) {
                foreach ($trophies_query->results() as $item) {
                    $this->_trophies[$item->type][$item->id] = new Trophy(null, null, $item);
                }
            }

            return $this->_trophies;
        })();
    }

    public function registerTrophy(TrophyBase $trophy) {
        $this->_trophy_types[$trophy->name()] = $trophy;
    }

    public function getAll(): iterable {
        return $this->_trophy_types;
    }

    public function getTrophy(string $trophy): ?TrophyBase {
        if (array_key_exists($trophy, $this->_trophy_types)) {
            return $this->_trophy_types[$trophy];
        }

        return null;
    }
}