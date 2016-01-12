<?php
/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 08/01/16
 * Time: 14:56
 */


class SeqrTransaction extends ObjectModel
{
    public $id;
    public $id_seqr;
    public $id_payment;
    public $id_order;
    public $status;
    public $refund;
    public $amount;
    public $amount_refunded;
    public $time;
    public $qr_code;
    public $ers_reference;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'seqr',
        'primary' => 'id_transaction',
        'fields' => array(
            'id_payment' =>         array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'status' =>             array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_seqr' =>             array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'qr_code' =>             array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'refund' =>             array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'time' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'amount' =>             array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'amount_refunded' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'ers_reference' =>      array('type' => self::TYPE_STRING, 'validate' => 'isString'),
        ),
    );

    /**
     * SeqrTransaction constructor.
     */
    public function __construct($id_seqr)
    {
        parent::__construct($id_seqr);
    }


}