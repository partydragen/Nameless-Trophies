<?php

namespace Trophies\Listeners;

use Referrals\Events\ReferralRegistrationEvent;
use UserTrophies;
use User;
use DB;

class UserReferralRegistrationListener {
    public static function execute(ReferralRegistrationEvent $event): void {
        $referral = $event->referral;

        $referral_owner = new User($referral->data()->user_id);
        $user_trophies = new UserTrophies($referral_owner);

        $referral_registrations = DB::getInstance()->query('SELECT count(*) AS c FROM nl2_referrals_registrations WHERE referral_id = ?', [$referral->data()->id])->first()->c;
        $user_trophies->checkTrophyStatus('referralRegistrations', $referral_registrations);
    }
}