<?php

include_once(dirname(__FILE__) . "/../../lib/prestashop/PsSeqrService.php");

/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 10/10/14
 * Time: 14:43
 */
class SeqrPaymentCodeModuleFrontController extends ModuleFrontControllerCore {

    private $service = null;
    private $error= false;

    function __construct() {

        parent::__construct();

        $this->ssl = true;
        $this->display_column_left = false;
        $this->display_column_right = false;
    }

    public function process() {

        parent::process();

        if (!$this->module->active) {
            return;
        }

        try {
            $this->validate();
            $this->placeOrder();
            $this->service = $this->createService();
            $this->service->sendInvoice();

        } catch (Exception $e) {
            PrestaShopLogger::addLog("Exception occurred when talking with SEQR", 3, null, $e);
            $this->error = true;
        }
    }

    public function initContent() {

        parent::initContent();

        if ($this->error) {
            $this->showPaymentFailed();
            return;
        }

        $cart = $this->context->cart;
        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'orderId' => $this->service->getOrderId(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'this_path' => $this->module->getPathUri(),
            'this_path_bw' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'module/seqr/',
            'webPluginUrl' => $this->service->getWebPluginUrl()
        ));

        $this->setTemplate('payment_code.tpl');

    }

    /**
     * Validates the current step in the order process.
     * @return mixed
     */
    private function validate() {

        $cart = $this->context->cart;
        if ($cart->id_customer == 0
            || $cart->id == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active)

            Tools::redirect('index.php?controller=order&step=1');

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'seqr') {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->module->l('This payment method is not available.', 'validation'));
    }

    /**
     * Places order with the PREPARATION status.
     * @internal param $cart
     */
    private function placeOrder() {

        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        // place order with issued status
        $this->module->validateOrder(
            $cart->id,
            Configuration::get("PS_OS_PREPARATION"),
            $total, $this->module->displayName,
            null,
            null,
            (int)$currency->id,
            false,
            $customer->secure_key
        );
    }

    /**
     * @return PsSeqrService
     */
    private function createService() {

        $cart = $this->context->cart;
        $orderId = OrderCore::getOrderByCartId($cart->id);
        $order = new Order($orderId);
        $service = new PsSeqrService($this->module->config, $order);

        return $service;
    }

    private function showPaymentFailed() {
        $this->context->smarty->assign(array(
            'shopUrl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__
        ));
        $this->setTemplate('payment_failed.tpl');
    }
}