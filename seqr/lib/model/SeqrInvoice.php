<?php

/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 03/11/14
 * Time: 11:02
 */
class SeqrInvoice {


    private $id = null;
    private $invoiceNumber = null;

    private $items = null;
    private $orderCurrencyCode = null;
    private $totalPriceInclTax = null;
    private $shippingInclTax = null;
    private $shippingTaxAmount = null;
    private $discountAmount = null;
    private $shippingDiscountAmount = null;
    private $backUrl = null;
    private $notificationUrl = null;

    /**
     * @return null
     */
    public function getInvoiceNumber() {
        return $this->invoiceNumber;
    }

    /**
     * @param null $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber) {
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * @return null
     */
    public function getBackUrl() {
        return $this->backUrl;
    }

    /**
     * @param null $backUrl
     */
    public function setBackUrl($backUrl) {
        $this->backUrl = $backUrl;
    }

    /**
     * @return null
     */
    public function getNotificationUrl() {
        return $this->notificationUrl;
    }

    /**
     * @param null $notificationUrl
     */
    public function setNotificationUrl($notificationUrl) {
        $this->notificationUrl = $notificationUrl;
    }


    /**
     * @return null
     */
    public function getDiscountAmount() {
        return $this->discountAmount;
    }

    /**
     * @param null $discountAmount
     */
    public function setDiscountAmount($discountAmount) {
        $this->discountAmount = $discountAmount;
    }


    /**
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param null $items
     */
    public function setItems($items) {
        $this->items = $items;
    }

    /**
     * @return null
     */
    public function getOrderCurrencyCode() {
        return $this->orderCurrencyCode;
    }

    /**
     * @param null $orderCurrencyCode
     */
    public function setOrderCurrencyCode($orderCurrencyCode) {
        $this->orderCurrencyCode = $orderCurrencyCode;
    }

    /**
     * @return null
     */
    public function getTotalPriceInclTax() {
        return $this->totalPriceInclTax;
    }

    /**
     * @param null $totalPriceInclTax
     */
    public function setTotalPriceInclTax($totalPriceInclTax) {
        $this->totalPriceInclTax = $totalPriceInclTax;
    }


    /**
     * @return null
     */
    public function getShippingDiscountAmount() {
        return $this->shippingDiscountAmount;
    }

    /**
     * @param null $shippingDiscountAmount
     */
    public function setShippingDiscountAmount($shippingDiscountAmount) {
        $this->shippingDiscountAmount = $shippingDiscountAmount;
    }

    /**
     * @return null
     */
    public function getShippingInclTax() {
        return $this->shippingInclTax;
    }

    /**
     * @param null $shippingInclTax
     */
    public function setShippingInclTax($shippingInclTax) {
        $this->shippingInclTax = $shippingInclTax;
    }

    /**
     * @return null
     */
    public function getShippingTaxAmount() {
        return $this->shippingTaxAmount;
    }

    /**
     * @param null $shippingTaxAmount
     */
    public function setShippingTaxAmount($shippingTaxAmount) {
        $this->shippingTaxAmount = $shippingTaxAmount;
    }
}

class SeqrItem {

    private $name = null;
    private $sku = null;
    private $quantity = null;
    private $taxRate = null;
    private $priceInclTax = null;
    private $totalPriceInclTax = null;
    private $unit = null;

    /**
     * @return null
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @param null $unit
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /**
     * @return null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getPriceInclTax() {
        return $this->priceInclTax;
    }

    /**
     * @param null $priceInclTax
     */
    public function setPriceInclTax($priceInclTax) {
        $this->priceInclTax = $priceInclTax;
    }

    /**
     * @return null
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param null $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return null
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * @param null $sku
     */
    public function setSku($sku) {
        $this->sku = $sku;
    }

    /**
     * @return null
     */
    public function getTaxRate() {
        return $this->taxRate;
    }

    /**
     * @param null $taxRate
     */
    public function setTaxRate($taxRate) {
        $this->taxRate = $taxRate;
    }

    /**
     * @return null
     */
    public function getTotalPriceInclTax() {
        return $this->totalPriceInclTax;
    }

    /**
     * @param null $totalPriceInclTax
     */
    public function setTotalPriceInclTax($totalPriceInclTax) {
        $this->totalPriceInclTax = $totalPriceInclTax;
    }

}


