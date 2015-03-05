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
            $payment->transaction_id = Tools::jsonEncode($data->toJson());
            $payment->save();
        }
    }

    protected function getSeqrData() {

        $payment = $this->getPaymentObject();

        if($this->loaded && $payment) {
            return new SeqrData(Tools::jsonDecode($payment->transaction_id));
        }
        return null;
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