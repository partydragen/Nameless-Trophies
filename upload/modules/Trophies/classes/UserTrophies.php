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
            $trophies_query = DB::getInstance()->query('SELECT nl2_trophies.*, trophy_id, received FROM `nl2_users_trophies` INNER JOIN nl2_trophies ON trophy_id = nl2_trophies.id WHERE user_id = ?', [$this->_user->data()->id]);
            if ($trophies_query->count()) {
                foreach ($trophies_query->results() as $item) {
                    $this->_trophies_data[$item->trophy_id] = $item;
                }
            }

            self::$_user_trophies_cache[$user->data()->id] = $this->_trophies_data;
        }
    }

    public function getTrophies(): array {
        $trophies = [];
        foreach ($this->_trophies_data as $trophy) {
            $trophies[$trophy->trophy_id] = new Trophy(null, null, $trophy);
        }

        return $trophies;
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

        EventHandler::executeEvent(new Trophies\Events\UserTrophyReceivedEvent(
            $this->_user,
            $trophy
        ));

        self::$_user_trophies_cache[$this->_user->data()->id][$trophy->data()->id] = [];

        // Reward any groups
        $groups = json_decode($trophy->data()->reward_groups, true);
        foreach ($groups as $group) {
            $this->_user->addGroup($group);
        }

        // Reward any credits?
        if ($trophy->data()->reward_credits_cents > 0 && Util::isModuleEnabled('Store')) {
            $customer = new Customer($this->_user);
            $customer->addCents($trophy->data()->reward_credits_cents);
        }

        // Alert user
        $trophies_language = new Language(ROOT_PATH . '/modules/Trophies/language', LANGUAGE);

        DB::getInstance()->insert('alerts', [
            'user_id' => $this->_user->data()->id,
            'type' => 'trophies',
            'url' => URL::build('/user/alerts'),
            'content_short' => $trophies_language->get('general', 'received_trophy', [
                'trophy' => $trophy->data()->title
            ]),
            'content' => $trophies_language->get('general', 'received_trophy', [
                'trophy' => $trophy->data()->title
            ]) . (($trophy->data()->reward_credits_cents > 0 && Util::isModuleEnabled('Store')) ? ' (' . $trophies_language->get('general', 'rewarded_x_for_completion', [
                'rewarded' => Store::fromCents($trophy->data()->reward_credits_cents) . ' Store Credits'
            ]) . ')' : ''),
            'created' => date('U')
        ]);

        return true;
    }
}