<?php

include_once(dirname(__FILE__) . "/../../lib/service/SeqrService.php");
include_once(dirname(__FILE__) . "/../../lib/prestashop/PsFactory.php");

class PsSeqrService extends SeqrService {


    public function PsSeqrService(SeqrConfig $config, $order = null) {

        parent::__construct($config, new PsFactory());
        if (isset($order)) {
            $this->createInvoice($order);
        }
    }

    protected function saveSeqrData(SeqrData $data) {

        $payment = $this->getPaymentObject();

        if (isset($payment) && isset($data)) {
            $payment->transaction_id = Tools::jsonEncode($data->toJson());
            $payment->save();
        }
    }

    protected function getSeqrData() {

        $payment = $this->getPaymentObject();

        if($this->loaded && $payment) {
            return new SeqrData(Tools::jsonDecode($payment->transaction_id));
        }
        return null;
    }

    private function getPaymentObject() {

        $this->throwExceptionIfNotLoaded();
        return OrderPayment::getByInvoiceId($this->order->getInvoiceNumber())->getFirst();
    }

    public function getCheckStatusUrl() {
        return urlencode(
            Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__
            . '/?fc=module&module=seqr&controller=checkstatus&orderId=' . $this->order->getId()
        );
    }

    public function getBackUrl() {
        return Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__
            . '/?fc=module&module=seqr&controller=confirmation&orderId=' . $this->order->getId();
    }
}