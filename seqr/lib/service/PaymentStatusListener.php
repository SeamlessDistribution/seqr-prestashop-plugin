<?php

/**
 *
 *
 *
 * User: kmanka
 * Date: 06/11/14
 * Time: 14:02
 */
interface PaymentStatusListener {

    public function onPaymentStatusChange($orderId, $status);
}