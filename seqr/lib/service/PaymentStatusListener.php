<?php

interface PaymentStatusListener {

    public function onPaymentStatusChange($orderId, $status);
}