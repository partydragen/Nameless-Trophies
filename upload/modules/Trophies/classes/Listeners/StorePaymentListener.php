<?php

namespace Trophies\Listeners;

use PaymentCompletedEvent;
use UserTrophies;
use User;
use DB;

class StorePaymentListener {
    public static function execute(PaymentCompletedEvent $event): void {
        $payments = DB::getInstance()->query('SELECT nl2_store_payments.*, order_id, user_id FROM nl2_store_payments LEFT JOIN nl2_store_orders ON order_id=nl2_store_orders.id WHERE nl2_store_orders.from_customer_id = ? AND status_id = 1 ORDER BY created DESC', [$event->customer->data()->id]);
        if ($payments->count()) {
            $payments = $payments->results();
            $amount_cents = 0;

            foreach ($payments as $payment) {
                $amount_cents += $payment->amount_cents;
            }

            $user_trophies = new UserTrophies($event->customer->getUser());
            $user_trophies->checkTrophyStatus('storePurchases', count($payments));
            $user_trophies->checkTrophyStatus('storeMoneySpent', $amount_cents / 100);
        }
    }
}