<?php
/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 03/11/14
 * Time: 14:28
 */

include_once(dirname(__FILE__) . "/../model/SeqrInvoiceFactory.php");
include_once(dirname(__FILE__) . "/../model/SeqrInvoice.php");

class PsFactory extends SeqrInvoiceFactory {

    protected function createInvoice($order, SeqrInvoice &$seqrInvoice) {

        if (isset($order) && isset($seqrInvoice)) {

            $seqrInvoice->setId($order->id);
            $seqrInvoice->setInvoiceNumber($order->invoice_number);
            $seqrInvoice->setTotalPriceInclTax($order->total_paid_tax_incl);
            $seqrInvoice->setDiscountAmount($order->total_discounts_tax_incl);
            $seqrInvoice->setShippingInclTax($order->total_shipping_tax_incl);
            $seqrInvoice->setShippingTaxAmount($order->carrier_tax_rate);

            $currency = new Currency($order->id_currency);
            $seqrInvoice->setOrderCurrencyCode($currency->iso_code);

            $seqrInvoice->setBackUrl("http://localhost/back");
            $seqrInvoice->setNotificationUrl("http://localhost/notify");

        } else {
            throw new Exception("No order found");
        }
    }

    protected function createItem($orderItem, SeqrInvoiceItem &$seqrItem) {

        if(isset($orderItem) && isset($seqrItem)) {

            $seqrItem->setName($orderItem['product_name']);
            $seqrItem->setPriceInclTax($orderItem['unit_price_tax_incl']);
            $seqrItem->setQuantity($orderItem['product_quantity']);
            $seqrItem->setSku($orderItem['product_ean13']);
            $seqrItem->setTaxRate($orderItem['tax_rate']);
            $seqrItem->setTotalPriceInclTax(
                intval($orderItem['product_quantity']) * floatval($orderItem['unit_price_tax_incl'])
            );
            $seqrItem->setUnit(""); // todo: change unit type
        }
    }

    protected function getItems($order) {

        if (isset($order)) {
            return $order->getOrderDetailList();
        }
    }
}