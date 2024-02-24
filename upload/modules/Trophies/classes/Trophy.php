<?php
/**
 * Trophy class
 * https://partydragen.com
 *
 * @package NamelessMC\Trophy
 * @author Partydragen
 * @version 2.1.0
 * @license MIT
 */
class Trophy {

    private $_data;

    public function __construct(?string $value = null, ?string $field = 'id', object $query_data = null) {
        if (!$query_data && $value) {
            $data = DB::getInstance()->get('trophies', [$field, '=', $value]);
            if ($data->count()) {
                $this->_data = $data->first();
            }
        } else if ($query_data) {
            // Load data from existing query.
            $this->_data = $query_data;
        }
    }

    /**
     * Does this trophy exist?
     *
     * @return bool Whether the trophy exists (has data) or not.
     */
    public function exists(): bool {
        return (!empty($this->_data));
    }

    /**
     * @return object This trophy's data.
     */
    public function data(): object {
        return $this->_data;
    }

    public function shouldReceive(int $score): bool {
        if ($score >= $this->data()->score) {
            return true;
        }

        return false;
    }

    public function getImage(bool $full = false): string {
        return (isset($this->data()->image) && !is_null($this->data()->image) ? (($full ? rtrim(URL::getSelfURL(), '/') : '') . (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/uploads/trophies/' . Output::getClean(Output::getDecoded($this->data()->image))) : 'https://partydragen.com/core/assets/img/default_trophy.png');
    }
}