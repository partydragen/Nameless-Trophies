<?php
/**
 * UserTrophies class
 * https://partydragen.com
 *
 * @package NamelessMC\Trophies
 * @author Partydragen
 * @version 2.1.0
 * @license MIT
 */

class UserTrophies {
    private static array $_user_trophies_cache = [];

    private User $_user;
    private array $_trophies_data = [];

    public function __construct(User $user) {
        $this->_user = $user;

        if (isset(self::$_user_trophies_cache[$user->data()->id])) {
            $this->_trophies_data = self::$_user_trophies_cache[$user->data()->id];
        } else {
            $trophies_query = DB::getInstance()->query('SELECT * FROM nl2_users_trophies WHERE user_id = ?', [$this->_user->data()->id]);
            if ($trophies_query->count()) {
                foreach ($trophies_query->results() as $item) {
                    $this->_trophies_data[$item->trophy_id] = $item;
                }
            }

            self::$_user_trophies_cache[$user->data()->id] = $this->_trophies_data;
        }
    }

    public function checkTrophyStatus(string $trophy_type, int $score) {
        $trophies = Trophies::getInstance()->getTrophies();

        if (array_key_exists($trophy_type, $trophies)) {
            foreach ($trophies[$trophy_type] as $trophy) {
                if ($trophy->shouldReceive($score)) {
                    if (!$this->hasTrophy($trophy)) {
                        $this->rewardTrophy($trophy);
                    }
                }
            }
        }
        die();
    }

    /**
     * Does the user have the trophy?
     *
     * @param Trophy $trophy Trophy to give.
     *
     * @return bool True if they have it, False if they don't have it.
     */
    public function hasTrophy(Trophy $trophy): bool {
        return array_key_exists($trophy->data()->id, $this->_trophies_data);
    }

    /**
     * Reward trophy to user.
     *
     * @param Trophy $trophy Trophy to give.
     *
     * @return bool True on success, false if they already have it.
     */
    public function rewardTrophy(Trophy $trophy): bool {
        if (array_key_exists($trophy->data()->id, $this->_trophies_data)) {
            return false;
        }

        DB::getInstance()->query('INSERT INTO `nl2_users_trophies` (`user_id`, `trophy_id`, `received`) VALUES (?, ?, ?)', [
            $this->_user->data()->id,
            $trophy->data()->id,
            date('U')
        ]);

        self::$_user_trophies_cache[$this->_user->data()->id][$trophy->data()->id] = [];

        return true;
    }
}