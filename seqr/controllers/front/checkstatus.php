<?php

include_once(dirname(__FILE__) . "/../../lib/prestashop/PsSeqrService.php");
include_once(dirname(__FILE__) . '/../../lib/service/PaymentStatusListener.php');

class SeqrCheckStatusModuleFrontController extends ModuleFrontControllerCore implements PaymentStatusListener {

    public function __construct() {

        parent::__construct();
        $this->ssl = true;
    }

    public function init() {

        parent::init();

        if (!$this->module->active) {
            $this->respondWithError('Could not find order');
        }

        try {
            $orderId = Tools::getValue("orderId");
            if(! isset($orderId)) $this->respondWithError("No order found");

            $order = new Order($orderId);
            $service = new PsSeqrService($this->module->config, $order);
            $service->setPaymentStatusListener($this);

            $status = $service->getPaymentStatus();

            $this->respond(Tools::jsonEncode($status));

        } catch (Exception $e) {
            $this->respondWithError('Payment checking error');
        }

    }

    public function onPaymentStatusChange($orderId, $status) {

        $orderHistory = new OrderHistory();
        $orderHistory->id_order = $orderId;

        if ($status === SeqrConfig::SEQR_PAYMENT_PAID) {
            $this->updateOrderStatus($orderHistory, Configuration::get('PS_OS_PAYMENT'));
        }

        if ($status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            $this->updateOrderStatus($orderHistory, Configuration::get('PS_OS_CANCELED'));
        }
    }

    private function respond($data) {
        die($data);
    }

    private function respondWithError($message) {
        $this->respond(Tools::jsonEncode(array('error' => $message)));
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