<?php

include_once(dirname(__FILE__) . "/../api/SeqrApi.php");
include_once(dirname(__FILE__) . "/../model/SeqrInvoiceFactory.php");
include_once(dirname(__FILE__) . "/../config/SeqrConfig.php");

/**
 * Class SeqrService
 *
 */
abstract class SeqrService {

    protected $config = null;
    protected $invoiceFactory = null;
    protected $order = null;
    protected $loaded = false;
    protected $api = null;

    /**
     * Initializes major parts of the service, API and the factory object.
     * @param SeqrConfig $config
     * @param SeqrInvoiceFactory $invoiceFactory
     */
    public function SeqrService(SeqrConfig $config, SeqrInvoiceFactory $invoiceFactory) {

        $this->config = $config;
        $this->invoiceFactory = $invoiceFactory;
        $this->api = new SeqrApi($this->config);
    }

    /**
     * Gets the id of currently processed order.
     * @return mixed
     * @throws Exception
     */
    public function getOrderId() {

        $this->throwExceptionIfNotLoaded();
        return $this->order->getId();
    }

    /**
     * Sends invoice to the SEQR system and saves response in the database.
     * @return mixed
     * @throws Exception
     */
    public function sendInvoice() {

        $this->throwExceptionIfNotLoaded();

        $result = $this->api->sendInvoice($this->order);
        if (!$result) throw new Exception("Problem occurred when sending invoice to the SEQR server.");

        $data = new SeqrData();
        $data->time = microtime(true);
        $this->saveSeqrData($data->fromRawData($result));
        return $result;
    }

    /**
     * Refunds a previous payment, either part of it or the whole sum.
     * @param amount
     */
    public function refundPayment($amount) {
    	$this->throwExceptionIfNotLoaded();

    	$transaction = $this->getSeqrTransaction();
    	if ($amount == null || $amount > $transaction->amount - $transaction->amount_refunded)
    		throw new Exception("Invalid amount!");

    	$currencyCode = $this->order->getOrderCurrencyCode();
    	$ersReference = $transaction->ers_reference;
    	$result = $this->api->refundPayment($ersReference, $amount, $currencyCode);

    	if ($result->resultCode == "0") {
    		$transaction->amount_refunded += $amount;
    		$transaction->refund = 1;
    		$transaction->save();
    	}
    	
    	return $result;
    }
    
    /**
     * Gets last response received from the SEQR system.
     * @return mixed
     */
    public function getInvoiceData() {
        return $this->getSeqrData();
    }

    /**
     * Converts shop specific order to the unified version.
     * @param $order
     * @throws Exception
     */
    protected function createInvoice($order) {

        if ($order == null) {
            throw new Exception("No invoice found in the context.");
        }

        $this->order = $this->invoiceFactory->create($order);
        $this->order->setBackUrl($this->getBackUrl());
        $this->order->setNotificationUrl($this->getNotificationUrl());

        $this->loaded = true;
    }

    /**
     * Gets recent transaction status from the SEQR system
     * and saves it to the database.
     * Cancels invoice if the timeout exceeds the limit provided in the configuration.
     * @return mixed
     * @throws Exception
     */
    public function processPaymentStatus() {

        $this->throwExceptionIfNotLoaded();

        $seqrData = $this->getSeqrData();
        if (!$seqrData) throw new Exception("No seqr payment data found");

        $status = $seqrData->status;
        if ($status === SeqrConfig::SEQR_PAYMENT_PAID
            || $status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            return $seqrData;
        }

        // check timeout
        if ($this->timeoutExceeded($seqrData->time)) {
            $this->api->cancelInvoice($seqrData->ref);
        }

        // gets recent status
        $result = $this->api->getPaymentStatus($seqrData->ref, $seqrData->ver);

        // updates payment data
        $this->saveSeqrData($seqrData->fromRawData($result));

        $this->changeOrderStatus($result->status);

        return $result;
    }

    private function timeoutExceeded($time) {
        return microtime(true) - $time > $this->config->getTimeout();
    }

    /**
     * Gets QR code for current transaction.
     * @return mixed
     */
    public function getQrCode() {

        $data = $this->getSeqrData();
        return $data->qr;
    }

    /**
     * Gets SEQR payment url for mobile devices.
     * @return mixed
     */
    public function getSeqrUrl() {

        return preg_replace('/^HTTP\:\/\//',
            $this->config->isDemoMode() ? 'SEQR-DEMO://' : 'SEQR://',
            $this->getQrCode()
        );
    }

    /**
     * The WebShop plugin definition.
     * @return string
     */
    public function getWebPluginUrl() {
        return 'https://cdn.seqr.com/webshop-plugin/js/seqrShop.js' .
        '#!' . ($this->config->isDemoMode() ? 'mode=demo' : '') .
        '&injectCSS=true&statusCallback=seqrStatusUpdated&' .
        'invoiceQRCode=' . $this->getQrCode() . '&' .
        'statusURL=' . $this->getCheckStatusUrl();
    }

    /**
     * Gets location where the WebShop plugin must poll to receive the recent payment status.
     * @return mixed
     */
    abstract public function getCheckStatusUrl();

    /**
     * Saves SEQR transaction data in the database.
     * @param SeqrData $data
     * @return mixed
     */
    abstract protected function saveSeqrData(SeqrData $data);

    /**
     * Gets the last saved transaction data.
     * @return mixed
     */
    abstract protected function getSeqrData();
    
    abstract protected function getSeqrTransaction();

    /**
     * Throws exception when the application specific order was not provided.
     * The order is obligatory.
     * @throws Exception
     */
    protected function throwExceptionIfNotLoaded() {
        if (!$this->loaded) {
            throw new Exception("Invoice object is not loaded");
        }
    }

    /**
     * Back url used on the mobile devices to redirect user
     * to the confirmation page.
     * @return mixed
     */
    public abstract  function getBackUrl();

    /**
     * Override this function to add notification functionality.
     * @return null
     */
    protected function getNotificationUrl() {
        return null;
    }

    /**
     * Changes order status in the database.
     * @param $status - payment status from SEQR side
     * @return mixed
     */
    public abstract function changeOrderStatus($status);
}

class SeqrData {

    /**
     * Constructs a data object from given JSON representation.
     * @param null $json
     */
    public function __construct($json = null) {
        if ($json) {
            if (isset($json->ref)) $this->ref = $json->ref;
            if (isset($json->status)) $this->status = $json->status;
            if (isset($json->ver)) $this->ver = $json->ver;
            if (isset($json->qr)) $this->qr = $json->qr;
            if (isset($json->time)) $this->time = $json->time;
            if (isset($json->ersRef)) $this->ersRef = $json->ersRef;
        }
    }

    /**
     * Populates the data object with some row data.
     * @param $rawData
     * @return $this
     */
    public function fromRawData($rawData) {
        if ($rawData) {
            if (isset($rawData->status)) $this->status = $rawData->status;
            if (isset($rawData->version)) $this->ver = $rawData->version;
            if (isset($rawData->invoiceQRCode)) $this->qr = $rawData->invoiceQRCode;
            if (isset($rawData->invoiceReference)) $this->ref = $rawData->invoiceReference;
            if (isset($rawData->ersReference)) $this->ersRef = $rawData->ersReference;

        }
        return $this;
    }

    /**
     * Converts data to the JSON representation.
     * @return array
     */
    public function toJson() {
        return array(
            "ref" => $this->emptyIfNull($this->ref),
            "status" => $this->emptyIfNull($this->status),
            "ver" => $this->emptyIfNull($this->ver),
            "qr" => $this->emptyIfNull($this->qr),
            "time" =>$this->emptyIfNull($this->time),
            "ersRef" =>$this->emptyIfNull($this->ersRef)
        );
    }

    private function emptyIfNull($value) {
        return is_null($value) ? "" : $value;
    }

    public $ref = null;
    public $ersRef = null;
    public $status = null;
    public $ver = null;
    public $qr = null;
    public $time = null;
}