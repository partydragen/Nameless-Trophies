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
}