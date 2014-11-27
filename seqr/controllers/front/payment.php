<?php

require_once(dirname(__FILE__) . "/../../lib/prestashop/package.php");

/**
 * Class SeqrPaymentController
 *
 * Controller prints payment summary page.
 */
class SeqrPaymentModuleFrontController extends PsSeqrFrontController {

    /**
     * Initializes payment summary page.
     */
    public function initContent() {

        parent::initContent();

        $cart = $this->context->cart;
        $currency = new Currency($cart->id_currency);

        $this->assignBreadcrumb();
        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'currency' => $currency,
            'total' => $cart->getOrderTotal(true, Cart::BOTH)
        ));

        $this->setTemplate('payment.tpl');
    }
}