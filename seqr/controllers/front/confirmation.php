<?php

include_once(dirname(__FILE__) . "/../../lib/prestashop/PsSeqrService.php");

class SeqrConfirmationModuleFrontController extends ModuleFrontControllerCore {


    public function __construct() {

        parent::__construct();

        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->ssl = true;
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

        $this->context->smarty->assign(array(
            'shopVersion' => $this->module->getShopVersion(),
            'navigation' => _PS_MODULE_DIR_ . "seqr/views/templates/front/navigation.tpl",
            'breadcrumb' => _PS_MODULE_DIR_ . "seqr/views/templates/front/breadcrumb.tpl"
        ));

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