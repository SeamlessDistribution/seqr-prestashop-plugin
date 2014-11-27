<?php

require_once(dirname(__FILE__) . "/../../lib/prestashop/package.php");

/**
 * Class SeqrConfirmationModuleFrontController
 *
 * Checks the last status for order and prints a page with the status information.
 */
class SeqrConfirmationModuleFrontController extends PsSeqrFrontController {


    public function __construct() {

        parent::__construct();

        $this->assignBreadcrumb();
        $this->assignNavigation();
    }

    public function initContent() {

        parent::initContent();

        $orderId = Tools::getValue("orderId");

        if (!$this->module->active || !isset($orderId)) {
            $this->failed();
            return;
        }

        $order = new Order($orderId);
        $service = new PsSeqrService($this->module->config, $order);

        $seqrData = $service->getInvoiceData();

        if ($seqrData->status === SeqrConfig::SEQR_PAYMENT_PAID) {
            $this->succeed();
        } else if ($seqrData->status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            $this->cancelled();
        } else {
            $this->failed();
        }
    }

    public function failed() {
        $this->setTemplate('payment_failed.tpl');
    }

    private function succeed() {
        $this->setTemplate('payment_succeed.tpl');
    }

    private function cancelled() {
        $this->setTemplate('payment_cancelled.tpl');
    }

} 