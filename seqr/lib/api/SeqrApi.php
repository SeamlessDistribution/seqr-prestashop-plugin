<?php

include_once(dirname(__FILE__) . "/../model/SeqrInvoice.php");

final class SeqrApi {

    private $config = null;

    function SeqrApi(SeqrConfig $config) {

        $this->config = $config;
    }

    public function sendInvoice(SeqrInvoice $invoice) {

        try {
            $SOAP = $this->SOAP();
            $result = $SOAP->sendInvoice(array(
                'context' => $this->getRequestContext(),
                'invoice' => $this->getInvoiceRequest($invoice)
            ))->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . " : " . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            throw new Exception("SEQR API - Send invoice error: ", 100, $e);
        }
    }

    public function getPaymentStatus($reference, $version) {

        try {
            $SOAP = $this->SOAP();
            $result = $SOAP->getPaymentStatus(array(
                "context" => $this->getRequestContext(),
                "invoiceReference" => $reference,
                "invoiceVersion" => $version ? $version : 0
            ))->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . ' : ' . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            throw new Exception("SEQR API - Get payment status error: ", 110, $e);
        }
    }

    public function getRequestContext() {

        return array(
            'initiatorPrincipalId' => array(
                'id' => $this->config->getTerminalId(),
                'type' => 'TERMINALID',
                'userId' => $this->config->getUserId()
            ),
            'password' => $this->config->getTerminalPass(),
            'clientRequestTimeout' => '0'
        );
    }

    private function SOAP() {

        return new SoapClient($this->config->getWsdl(), array('trace' => 1, 'connection_timeout' => 3000));
    }

    private function getInvoiceRequest(SeqrInvoice $order) {

        $currencyCode = $order->getOrderCurrencyCode();
        $unitType = "unit";

        // Prepare main part of request data (ex Shipping and Discounts)

        $thisObj = $this;
        $invoice = array(
            'paymentMode' => 'IMMEDIATE_DEBIT',
            'acknowledgmentMode' => 'NO_ACKNOWLEDGMENT',

            'issueDate' => date('Y-m-d\Th:i:s'),
            'title' => "SEQR payment",
            'clientInvoiceId' => $order->getId(),

            'invoiceRows' => array_map(function (SeqrItem $item) use (&$thisObj, $currencyCode, $unitType) {
                return array(
                    'itemDescription' => $item->getName(),
                    'itemSKU' => $item->getSku(),
                    'itemTaxRate' => $item->getTaxRate(),
                    'itemUnit' => $unitType,
                    'itemQuantity' => $item->getQuantity(),
                    'itemUnitPrice' => array(
                        'currency' => $currencyCode,
                        'value' => $thisObj->toFloat($item->getPriceInclTax())
                    ),
                    'itemTotalAmount' => array(
                        'currency' => $currencyCode,
                        'value' => $thisObj->toFloat($item->getTotalPriceInclTax())
                    )
                );
            }, $order->getItems()),

            'totalAmount' => array(
                'currency' => $currencyCode,
                'value' => $this->toFloat($order->getTotalPriceInclTax())
            ),

            'backURL' => $order->getBackUrl(),
            'notificationUrl' => $order->getNotificationUrl()
        );

        // Shipping & Handling
        if ($order->getShippingInclTax() && intval($order->getShippingInclTax())) {
            $invoice['invoiceRows'][] = array(
                'itemDescription' => 'Shipping & Handling',
                'itemQuantity' => 1,
                'itemTaxRate' => $order->getShippingTaxAmount(),
                'itemUnit' => '',
                'itemTotalAmount' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getShippingInclTax())
                ),
                'itemUnitPrice' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getShippingInclTax())
                )
            );
        }

        // Discount
        if ($order->getDiscountAmount() && intval($order->getDiscountAmount())) {
            $invoice['invoiceRows'][] = array(
                'itemDescription' => 'Discount',
                'itemQuantity' => 1,
                'itemTaxRate' => $order->getShippingTaxAmount(),
                'itemUnit' => '',
                'itemTotalAmount' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getDiscountAmount())
                ),
                'itemUnitPrice' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getDiscountAmount())
                )
            );
        }

        // Shipping discount
        if ($order->getShippingDiscountAmount() && intval($order->getShippingDiscountAmount())) {
            $invoice['invoiceRows'][] = array(
                'itemDescription' => 'Shipping Discount',
                'itemQuantity' => 1,
                'itemTaxRate' => $order->getShippingTaxAmount(),
                'itemUnit' => '',
                'itemTotalAmount' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getShippingDiscountAmount())
                ),
                'itemUnitPrice' => array(
                    'currency' => $currencyCode,
                    'value' => $this->toFloat($order->getShippingDiscountAmount())
                )
            );
        }

        return $invoice;
    }

    /**
     * Cancels invoice on the SEQR server.
     * @param $reference
     * @return mixed
     * @throws Exception
     */
    public function cancelInvoice($reference) {

        try {
            $SOAP = $this->SOAP();
            $result = $SOAP->cancelInvoice(array(
                "context" => $this->getRequestContext(),
                "invoiceReference" => $reference
            ))->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . ' : ' . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            throw new Exception("SEQR API - Cancel invoice error: ", 120, $e);
        }
    }

    public function toFloat($number) {

        return number_format((float)$number, 2, '.', '');
    }
}
