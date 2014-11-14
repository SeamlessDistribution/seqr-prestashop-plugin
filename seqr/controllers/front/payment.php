<?php

/**
 * Class SeqrPaymentController
 *
 * Controller prints payment summary page.
 */
class SeqrPaymentModuleFrontController extends ModuleFrontControllerCore {


    public function __construct() {

        parent::__construct();

        $this->display_column_left = false;
        $this->display_column_right = false;
        $this->ssl = true;
    }

    /**
     * Initializes payment summary page.
     *
     * @throws PrestaShopException
     */
    public function initContent() {

        parent::initContent();

        $cart = $this->context->cart;
        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'shopVersion' => $this->module->getShopVersion(),
            'this_path' => $this->module->getPathUri(),
            'this_path_bw' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'module/'.$this->module->name.'/',
            'breadcrumb' => _PS_MODULE_DIR_ . "seqr/views/templates/front/breadcrumb.tpl"
        ));

        $this->setTemplate('payment.tpl');

    }

} 