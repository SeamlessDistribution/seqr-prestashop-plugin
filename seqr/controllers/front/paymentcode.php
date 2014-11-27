<?php

require_once(dirname(__FILE__) . "/../../lib/prestashop/package.php");

/**
 * Validates the cart, places the order and sends invoice to the SEQR server.
 * Then the SEQR code is printed and shown to the client.
 *
 * User: kmanka
 * Date: 10/10/14
 * Time: 14:43
 */
class SeqrPaymentCodeModuleFrontController extends PsSeqrFrontController {

    private $service = null;
    private $error= false;

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
            PrestaShopLogger::addLog("Exception occurred when sending invoice to SEQR", 3, null, $e);
            $this->service->changeOrderStatus(SeqrConfig::SEQR_PAYMENT_ERROR);
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
        $currency = new Currency($cart->id_currency);

        $this->assignBreadcrumb();
        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'currency' => $currency,
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'webPluginUrl' => $this->service->getWebPluginUrl(),
            'backUrl' => $this->service->getBackUrl(),
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
        $this->assignBreadcrumb();
        $this->assignNavigation();
        $this->setTemplate('payment_failed.tpl');
    }
}