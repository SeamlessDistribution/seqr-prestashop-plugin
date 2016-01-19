<?php

include_once(dirname(__FILE__) . "/../../lib/service/SeqrService.php");
include_once(dirname(__FILE__) . "/../../lib/prestashop/PsFactory.php");

class PsSeqrService extends SeqrService {


    public function PsSeqrService(SeqrConfig $config, $order = null) {

        parent::__construct($config, new PsFactory());
        if (isset($order)) {
            $this->createInvoice($order);
        }
    }

    protected function saveSeqrData(SeqrData $data) {

        $payment = $this->getPaymentObject();

        if (isset($payment) && isset($data)) {

            $transaction = new SeqrTransaction($payment->transaction_id);

            $transaction->amount = $payment->amount;
            $transaction->ers_reference = $data->ersRef;
            $transaction->id_seqr = $data->ref;
            $transaction->time =(int)$data->time;
            $transaction->status = $data->status;
            $transaction->id_payment = $payment->id;
            $transaction->id_order = $this->getOrderId();
            $transaction->qr_code = $data->qr;

            if (ValidateCore::isLoadedObject($transaction)) {
                $transaction->save();
            } else {
                $transaction->add();
                $payment->transaction_id = $transaction->id;
                $payment->save();
            }
        }
    }

    protected function getSeqrData() {

        $payment = $this->getPaymentObject();
        $transaction = new SeqrTransaction($payment->transaction_id);

        if($this->loaded && $payment && Validate::isLoadedObject($transaction)) {
            $data = new SeqrData();
            $data->qr = $transaction->qr_code;
            $data->ref = $transaction->id_seqr;
            $data->status = $transaction->status;
            $data->time = $transaction->time;

            return $data;
        }
        return null;
    }

    protected function getSeqrTransaction() {
    	$payment = $this->getPaymentObject();
    	$transaction = new SeqrTransaction($payment->transaction_id);
    	return $transaction;
    }
    
    private function getPaymentObject() {

        $this->throwExceptionIfNotLoaded();

        $orderId = $this->getOrderId();
        if (isset($orderId)) {
            $payments = OrderPayment::getByOrderId($orderId);
            if (is_array($payments) && count($payments) > 0) {
                return $payments[0];
            }
        }
        return null;
    }

    public function getCheckStatusUrl() {
        return urlencode(
            Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__
            . '?fc=module&module=seqr&controller=checkstatus&orderId=' . $this->order->getId()
        );
    }

    public function getBackUrl() {
        return Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__
            . '?fc=module&module=seqr&controller=confirmation&orderId=' . $this->order->getId();
    }

    public function changeOrderStatus($status) {

        $this->throwExceptionIfNotLoaded();

        $orderHistory = new OrderHistory();
        $orderHistory->id_order = intval($this->order->getId());

        if ($status === SeqrConfig::SEQR_PAYMENT_PAID) {
            $this->updateOrderStatus($orderHistory, Configuration::get('PS_OS_PAYMENT'));
        }

        if ($status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            $this->updateOrderStatus($orderHistory, Configuration::get('PS_OS_CANCELED'));
        }

        if ($status === SeqrConfig::SEQR_PAYMENT_ERROR) {
            $this->updateOrderStatus($orderHistory, Configuration::get('PS_OS_ERROR'));
        }
    }

    /**
     * Updates the order status in the database object.
     * @param $status
     * @param $orderHistory
     */
    private function updateOrderStatus($orderHistory, $status) {

        $orderHistory->changeIdOrderState($status, $orderHistory->id_order);
        $orderHistory->save();
    }
}