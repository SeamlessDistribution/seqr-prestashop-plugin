<?php

require_once(dirname(__FILE__) . "/../../lib/prestashop/package.php");

/**
 * Class SeqrCheckStatusModuleFrontController
 *
 * Accepts GET requests from the SEQR WebShop plugin which resides in the browser.
 * Processes the payment status.
 */
class SeqrCheckStatusModuleFrontController extends PsSeqrFrontController {

    public function init() {

        parent::init();

        if (!$this->module->active) {
            $this->respondWithError('Could not find order');
        }

        try {
            $orderId = Tools::getValue("orderId");
            if(!isset($orderId)) {
                $this->respondWithError("No order found");
            }

            $order = new Order($orderId);
            $service = new PsSeqrService($this->config, $order);

            $status = $service->processPaymentStatus();
            $this->respond(Tools::jsonEncode($status));

        } catch (Exception $e) {
            $this->respondWithError('Payment checking error');
        }

    }

    private function respond($data) {
        die($data);
    }

    private function respondWithError($message) {
        $this->respond(Tools::jsonEncode(array('error' => $message)));
    }
}