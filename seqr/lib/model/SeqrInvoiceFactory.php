<?php

/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 03/11/14
 * Time: 12:01
 */
abstract class SeqrInvoiceFactory {

    public function create($order) {

        $seqrInvoice = new SeqrInvoice();

        $this->createInvoice($order, $seqrInvoice);

        $items = $this->getItems($order);
        $invoiceItems = array();

        if (is_array($items)) {

            foreach($items as $item) {
                $newItem = new SeqrInvoiceItem();
                $this->createItem($item, $newItem);
                array_push($invoiceItems, $newItem);
            }
            $seqrInvoice->setItems($invoiceItems);
        }
        return $seqrInvoice;
    }

    protected abstract function createInvoice($order, SeqrInvoice &$seqrInvoice);

    protected abstract function createItem($orderItem, SeqrInvoiceItem &$seqrItem);

    protected abstract function getItems($order);

}